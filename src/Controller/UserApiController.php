<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users', name: 'api_users_')]
class UserApiController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserService $userService,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $users = $this->userRepository->findAll();
            return $this->json(['data' => $users], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation des données présentes
            if (!isset($data['prenom'], $data['nom'], $data['email'])) {
                return $this->json(
                    ['error' => 'Les champs nom, prenom et email sont obligatoires.'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Mapper les données dans le DTO
            $userDto = new UserDto(
                trim($data['prenom']),
                trim($data['nom']),
                trim($data['email'])
            );

            // Valider le DTO
            $errors = $this->validator->validate($userDto);
            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
            }

            // Créer l'utilisateur via le service
            $user = $this->userService->createUser($userDto);

            return $this->json(['data' => $user], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Une erreur est survenue lors de la création de l\'utilisateur.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return $this->json(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            // Mapper les données vers le DTO
            $userDto = new UserDto(
                trim($data['prenom']),
                trim($data['nom']),
                trim($data['email'])
            );

            // Valider le DTO
            $errors = $this->validator->validate($userDto);
            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
            }

            // Mettre à jour l'utilisateur via le service
            $updatedUser = $this->userService->updateUser($user, $userDto);

            return $this->json(['data' => $updatedUser], Response::HTTP_OK);
        } catch (EntityNotFoundException $e) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return $this->json(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
            }

            // Supprimer l'utilisateur via le service
            $this->userService->deleteUser($user);

            return $this->json(['message' => 'Utilisateur supprimé.'], Response::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Une erreur est survenue lors de la suppression de l\'utilisateur.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
