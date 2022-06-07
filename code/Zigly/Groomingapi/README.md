# Mage2 Module Zigly Groomingapi

    ``zigly/module-groomingapi``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Grooming API service

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Zigly`
 - Enable the module by running `php bin/magento module:enable Zigly_Groomingapi`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require zigly/module-groomingapi`
 - enable the module by running `php bin/magento module:enable Zigly_Groomingapi`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - API Endpoint
	- GET - Zigly\Groomingapi\Api\GetPetsManagementInterface > Zigly\Groomingapi\Model\GetPetsManagement

 - API Endpoint
	- GET - Zigly\Groomingapi\Api\GetPlanManagementInterface > Zigly\Groomingapi\Model\GetPlanManagement

 - API Endpoint
	- POST - Zigly\Groomingapi\Api\UserLocationManagementInterface > Zigly\Groomingapi\Model\UserLocationManagement

 - API Endpoint
	- POST - Zigly\Groomingapi\Api\AddPetManagementInterface > Zigly\Groomingapi\Model\AddPetManagement

 - API Endpoint
	- POST - Zigly\Groomingapi\Api\DeletePetManagementInterface > Zigly\Groomingapi\Model\DeletePetManagement


## Attributes



