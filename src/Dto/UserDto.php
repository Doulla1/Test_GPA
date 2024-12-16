<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Length(
        max: 100,
        normalizer: 'trim',
        maxMessage: 'Le prénom ne peut pas dépasser 100 caractères')]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ '-]+$/u",
        message: 'Le prénom ne doit contenir que des lettres, espaces, apostrophes ou tirets.'
    )]
    public string $prenom;

    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        max: 100,
        normalizer: 'trim',
        maxMessage: "Le nom est obligatoire"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ '-]+$/u",
        message: 'Le nom ne doit contenir que des lettres, espaces, apostrophes ou tirets.'
    )]
    public string $nom;

    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Length(
        max: 180,
        normalizer: 'trim',
        maxMessage: 'L\'email ne peut pas dépassser 180 caractères',
    )]
    #[Assert\Regex(
        pattern: "/^[^@]+@[^@]\.[a-zA-Z]{2,}$/",
        message: 'L\'email est invalide'
    )]
    public string $email;

    public function __construct(string $prenom, string $nom, string $email)
    {
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->email = $email;
    }
}