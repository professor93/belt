<?php
/**
 * Created by Sodikmirzo.
 * User: Sodikmirzo Sattorov ( https://github.com/Sodiqmirzo )
 * Date: 10/17/2022
 * Time: 5:22 PM
 */

namespace Uzbek\Belt\Dtos;

class Customer
{
    public string $inn;

    public string $pinfl;

    public string $firstName;

    public string $lastName;

    public string $middleName;

    public ?string $birthDate;

    public string $birthPlace;

    public string $birthCountry;

    public string $gender;

    public string $citizenship;

    public string $docType;

    public string $series;

    public string $number;

    public string $docIssueDate;

    public string $docExpireDate;

    public string $docIssuePlace;

    public string $residenceCountry;

    public string $codeFilial;

    public ?string $residenceRegion;

    public ?string $residenceDistrict;

    public ?string $residenceAddress;

    public ?string $phone;

    public ?string $mobilePhone;

    public ?string $email;

    public ?string $maritalStatus;

    //toArray method
    public function toArray(): array
    {
        return [
            'inn' => $this->inn,
            'pinfl' => $this->pinfl,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'middleName' => $this->middleName,
            'birthDate' => $this->birthDate,
            'birthPlace' => $this->birthPlace,
            'birthCountry' => $this->birthCountry,
            'gender' => $this->gender,
            'citizenship' => $this->citizenship,
            'docType' => $this->docType,
            'series' => $this->series,
            'number' => $this->number,
            'docIssueDate' => $this->docIssueDate,
            'docExpireDate' => $this->docExpireDate,
            'docIssuePlace' => $this->docIssuePlace,
            'residenceCountry' => $this->residenceCountry,
            'codeFilial' => $this->codeFilial,
            'residenceRegion' => $this->residenceRegion,
            'residenceDistrict' => $this->residenceDistrict,
            'residenceAddress' => $this->residenceAddress,
            'phone' => $this->phone,
            'mobilePhone' => $this->mobilePhone,
            'email' => $this->email,
            'maritalStatus' => $this->maritalStatus,
        ];
    }
}
