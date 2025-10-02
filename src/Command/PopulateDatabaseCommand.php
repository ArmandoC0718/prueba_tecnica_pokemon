<?php

namespace App\Command;

use App\Domain\Entity\Move;
use App\Domain\Entity\Pokemon;
use App\Domain\Entity\Type;
use App\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:populate-database',
    description: 'Poblar la base de datos con los datos de PokeAPI',
)]
class PopulateDatabaseCommand extends Command
{
    private string $pokeApiBaseUrl = 'https://pokeapi.co/api/v2/';
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = HttpClient::create();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->title('Ingresando los datos a la base de datos');

            // Crear tipos de pokemon
            $io->section('Ingresando tipos de pokemon...');
            $types = $this->populateTypes($io);
            $io->success(sprintf('Se han creado %d tipos', count($types)));

            // Crear movimientos de los pokemones
            $io->section(message: 'Ingresando movimientos...');
            $moves = $this->populateMoves($types, $io);
            $io->success(sprintf('Se han registrado %d movimientos', count($moves)));

            $io->section('Ingresando pokemones...');
            $pokemons = $this->populatePokemons($types, $moves, $io);
            $io->success(sprintf('Se han creado %d Pokemons', count($pokemons)));

            $io->section('Creando usuarios...');
            $users = $this->createUser($io);
            $io->success(sprintf('Se han creado %d usuarios', count($users)));

            $this->entityManager->flush();

            $io->section('La base de datos a sido poblada de forma exitosa!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function populateTypes(SymfonyStyle $io): array
    {
        $types = [];

        try {
            $response = $this->httpClient->request('GET', url: "{$this->pokeApiBaseUrl}type?limit=20");
            $data = $response->toArray();

            foreach ($data['results'] as $typeData) {
                // Verificar si el pokemon ya existe en la BD
                $existingType = $this->entityManager
                    ->getRepository(Type::class)
                    ->findOneBy(['name' => $typeData['name']]);

                if (!$existingType) {
                    $type = new Type();
                    $type->setName($typeData['name']);
                    $this->entityManager->persist($type);
                    $types[] = $type;
                } else {
                    $types[] = $existingType;
                }
            }

            $this->entityManager->flush();
            return $types;

        } catch (\Exception $e) {
            $io->error("Error al obtener los tipos de pokemones " . $e->getMessage());
            throw $e;
        }
    }

    private function populateMoves(array $types, SymfonyStyle $io): array
    {
        $moves = [];

        try {
            $response = $this->httpClient->request('GET', "{$this->pokeApiBaseUrl}move?limit=20");
            $data = $response->toArray();

            foreach ($data['results'] as $moveData) {

                try {
                    $moveResponse = $this->httpClient->request('GET', $moveData['url']);
                    $moveData = $moveResponse->toArray();

                    $existingMove = $this->entityManager
                        ->getRepository(Move::class)
                        ->findOneBy(['name' => $moveData['name']]);

                    if (!$existingMove) {
                        $move = new Move();
                        $move->setName($moveData['name']);

                        $moveTypeName = $moveData['type']['name'];
                        $moveType = $this->findTypeByName($types, $moveTypeName);

                        if ($moveType) {
                            $move->setType($moveType);
                            $this->entityManager->persist($move);
                            $moves[] = $move;
                        }
                    } else {
                        $moves[] = $existingMove;
                        $io->text("El movimiento ya existe");
                    }
                } catch (\Exception $e) {
                    $io->text("Ocurrio un error al procesar el movimiento " . $moveData['name']);
                    continue;
                }
            }

            $this->entityManager->flush();
            return $moves;

        } catch (\Exception $e) {
            $io->error("Error al obtener los movimientos de los pokemones: " . $e->getMessage());
            throw $e;
        }
    }

    private function populatePokemons(array $types, array $moves, SymfonyStyle $io): array
    {
        $pokemons = [];

        try {
            $response = $this->httpClient->request('GET', "{$this->pokeApiBaseUrl}pokemon?limit=15");
            $data = $response->toArray();

            foreach ($data['results'] as $pokeData) {

                $existingPokemon = $this->entityManager
                    ->getRepository(Pokemon::class)
                    ->findOneBy(['name' => $pokeData['name']]);

                if (!$existingPokemon) {
                    $pokemon = new Pokemon();
                    $pokemon->setName($pokeData['name']);
                    $pokemon->setLevel(rand(1, 50));

                    try {
                        $pokemonDetailResponse = $this->httpClient->request('GET', $pokeData['url']);
                        $pokemonDetail = $pokemonDetailResponse->toArray();

                        foreach ($pokemonDetail['stats'] as $stat) {
                            switch ($stat['stat']['name']) {
                                case 'hp':
                                    $pokemon->setHealthPoints($stat['base_stat']);
                                    break;
                                case 'attack':
                                    $pokemon->setAttack($stat['base_stat']);
                                    break;
                                case 'defense':
                                    $pokemon->setDefense($stat['base_stat']);
                                    break;
                                case 'speed':
                                    $pokemon->setSpeed($stat['base_stat']);
                                    break;
                            }
                        }

                        //Obtener el valor de Catch Rate 
                        try {
                            $pokemonSpeciesResponse = $this->httpClient->request('GET', $pokemonDetail['species']['url']);
                            $pokemonSpecies = $pokemonSpeciesResponse->toArray();

                            if (isset($pokemonSpecies['capture_rate'])) {
                                $pokemon->setCatchRate($pokemonSpecies['capture_rate']);
                            }


                        } catch (\Exception $e) {
                            $io->text("Ocurrio un error al obtener el catch rate del pokemon " . $pokemon->getName() . " : " . $e->getMessage());
                            continue;
                        }

                        //Asignar tipos del pokemon
                        foreach ($pokemonDetail['types'] as $typeInfo) {
                            $typeName = $typeInfo['type']['name'];
                            $pokemonType = $this->findTypeByName($types, $typeName);
                            if ($pokemonType) {
                                $pokemon->addType($pokemonType);
                            }
                        }

                        // Asignar movimientos al pokemon
                        $this->assignMovesToPokemon($pokemon, $moves, $io);

                        $this->entityManager->persist($pokemon);
                        $pokemons[] = $pokemon;

                    } catch (\Exception $e) {
                        $io->error("Ocurrio un error al guardar el pokemon" . $e->getMessage());
                        throw $e;
                    }
                } else {
                    // Si el Pokemon existe pero no tiene movimientos, asignar algunos
                    if ($existingPokemon->getMoves()->isEmpty()) {
                        $this->assignMovesToPokemon($existingPokemon, $moves, $io);
                        $this->entityManager->persist($existingPokemon);
                    }
                    $pokemons[] = $existingPokemon;
                    $io->text("El pokemon ya existe");
                }
            }

            $this->entityManager->flush();
            return $pokemons;

        } catch (\Exception $e) {
            $io->error("Error al obtener los pokemones: " . $e->getMessage());
            throw $e;
        }
    }

    private function createUser(SymfonyStyle $io): array
    {
        $users = [];

        try {
            $userData = [
                [
                    'username' => 'ash_ketchum',
                    'password' => 'pikachu123',
                    'rol' => 'ROLE_TRAINER'
                ],
                [
                    'username' => 'misty_waterflower',
                    'password' => 'starmie456',
                    'rol' => 'ROLE_TRAINER'
                ],
                [
                    'username' => 'professor_oak',
                    'password' => 'pokedex789',
                    'rol' => 'ROLE_PROFESSOR'
                ]
            ];

            foreach ($userData as $userInfo) {
                $existingUser = $this->entityManager
                    ->getRepository(User::class)
                    ->findOneBy(['username' => $userInfo['username']]);

                if (!$existingUser) {
                    $user = new User();
                    $user->setUsername($userInfo['username']);
                    // En un entorno real, la contraseña debe ser hasheada
                    $user->setPassword(password_hash($userInfo['password'], algo: PASSWORD_BCRYPT));

                    $user->setRoles([$userInfo['rol']]);
                    $this->entityManager->persist($user);
                    $users[] = $user;
                } else {
                    $users[] = $existingUser;
                    $io->text(message: "El usuario ya existe");
                }
            }

            $this->entityManager->flush();
            return $users;
        } catch (\Exception $e) {
            $io->error("Error al crear los usuarios: " . $e->getMessage());
            throw $e;
        }
    }

    private function assignMovesToPokemon(Pokemon $pokemon, array $moves, SymfonyStyle $io): void
    {
        try {
            // Get Pokemon's types for move compatibility
            $pokemonTypes = [];
            foreach ($pokemon->getTypes() as $type) {
                $pokemonTypes[] = strtolower($type->getName());
            }

            // Filter compatible moves
            $compatibleMoves = [];
            foreach ($moves as $move) {
                $moveType = strtolower($move->getType()->getName());
                
                // Allow normal type moves for all Pokemon
                if ($moveType === 'normal') {
                    $compatibleMoves[] = $move;
                    continue;
                }
                
                // Allow moves that match Pokemon's type
                if (in_array($moveType, $pokemonTypes)) {
                    $compatibleMoves[] = $move;
                }
            }

            // If no compatible moves found, assign some normal type moves
            if (empty($compatibleMoves)) {
                foreach ($moves as $move) {
                    if (strtolower($move->getType()->getName()) === 'normal') {
                        $compatibleMoves[] = $move;
                        if (count($compatibleMoves) >= 4) break;
                    }
                }
            }

            // Assign random moves (max 4)
            $selectedMoves = array_rand($compatibleMoves, min(4, count($compatibleMoves)));
            if (!is_array($selectedMoves)) {
                $selectedMoves = [$selectedMoves];
            }

            foreach ($selectedMoves as $moveIndex) {
                $pokemon->addMove($compatibleMoves[$moveIndex]);
            }

            $io->text(sprintf(
                "Assigned %d moves to %s (types: %s)", 
                count($selectedMoves),
                $pokemon->getName(),
                implode(', ', $pokemonTypes)
            ));

        } catch (\Exception $e) {
            $io->warning("Error assigning moves to {$pokemon->getName()}: " . $e->getMessage());
        }
    }

    private function findTypeByName(array $types, string $name): ?Type
    {
        foreach ($types as $type) {
            if (strtolower($type->getName()) === strtolower($name)) {
                return $type;
            }
        }
        return null;
    }
}