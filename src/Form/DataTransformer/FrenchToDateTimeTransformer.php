<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface {
    public function transform($date) {
        if ($date === null) {
            return '';
        }

        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate) {
        // frenchDate == d/m/Y
        if ($frenchDate === null) {
            throw new TransformationFailedException('Date non fournit !');
        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if ($date == false) {
            throw new TransformationFailedException('Format de la date non valide !');
        }

        return $date;
    }
}