<?php


namespace App\Domain;

enum Role: string {
    case ETUDIANT = 'etudiant';
    case PILOTE = 'pilote';
    case ADMIN = 'admin';
}