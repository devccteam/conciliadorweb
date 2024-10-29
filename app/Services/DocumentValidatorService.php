<?php

namespace App\Services;

class DocumentValidatorService
{
    public function isValidDocument($document) {
        $document = preg_replace('/[^0-9]/', '', $document);
    
        if (strlen($document) === 11) {
            return $this->validateCPF($document);
        }

        if (strlen($document) === 14) {
            return $this->validateCNPJ($document);
        }
    
        return false;
    }
    
    public function validateCPF($cpf) {
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $firstCheckDigit = ($remainder < 2) ? 0 : 11 - $remainder;
    
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $secondCheckDigit = ($remainder < 2) ? 0 : 11 - $remainder;
    
        return ($cpf[9] == $firstCheckDigit && $cpf[10] == $secondCheckDigit);
    }
    
    function validateCNPJ($cnpj) {
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
    
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights1[$i];
        }
        $remainder = $sum % 11;
        $firstCheckDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights2[$i];
        }
        $remainder = $sum % 11;
        $secondCheckDigit = ($remainder < 2) ? 0 : 11 - $remainder;
    
        return ($cnpj[12] == $firstCheckDigit && $cnpj[13] == $secondCheckDigit);
    }

    public static function maskCpfCnpj($document, $type)
    {
        if ($type === 'company') {
            return static::maskCnpj($document);
        } 
        
        if ($type === 'individual') {
            return static::maskCpf($document);
        }

        if (strlen($document) === 11) {
            return static::maskCpf($document);
        }

        if (strlen($document) === 14) {
            return static::maskCnpj($document);
        }
        
        return $document;
    }

    public static function maskCpf($cpf)
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public static function maskCnpj($cnpj)
    {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
}
