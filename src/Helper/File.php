<?php
declare(strict_types = 1);

namespace App\Helper;

use Iterator;
use League\Csv\Reader;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class File
{
    /**
     * @param string $path
     * @return array
     */
    public function parseCsv(string $path = ''): array
    {
        if ($path === '') {
            throw new InvalidOptionException('The --file option requires a value.');
        }

        // read file from path
        $csvFile = Reader::createFromPath($path, 'r');
        if ($csvFile->count() === 0) {
            throw new InvalidOptionException('Could not read records, No such file.');
        }
        $csvFile->setHeaderOffset(0);
        return $this->validateRecords($csvFile->getRecords());
    }

    /**
     * @param Iterator $records
     * @return array
     */
    private function validateRecords(Iterator $records): array
    {
        $emailConstraint = new Assert\Email([
            'message' => 'The email {{ value }} is not a valid email.',
            'checkMX' => true,
        ]);
        $blankConstraint = new Assert\NotBlank(['message' => '{{ value }} should not be blank.']);

        $validator = Validation::createValidator();
        $validatedRecords = [];
        $errors = [];
        foreach ($records as $record) {
            $validRecord = true;
            $name = trim($record['name']) ?? '';
            $surname = trim($record['surname']) ?? '';
            $email = trim($record['email']) ?? '';
            $violations = $validator->validate($email, $emailConstraint);
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
                $validRecord = false;
            }
            $violations2 = $validator->validate($name, $blankConstraint);
            foreach ($violations2 as $violation) {
                $errors[] = 'Name ' . $violation->getMessage();
                $validRecord = false;
            }
            if (!$validRecord) {
                continue;
            }
            $validatedRecords[] = [ucfirst(strtolower($name)), ucfirst(strtolower($surname)), $email];

        }
        return [$errors, $validatedRecords];
    }
}
