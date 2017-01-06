@ECHO OFF
setlocal EnableDelayedExpansion

ECHO Start to package SLS php sdk...

SET SUCCESS_RET=0
SET ERROR_NO_SETUP_FILE=1
SET ERROR_NO_VERSION=2
SET ERROR_NO_DOCS=3
SET ERROR_ROBOCOPY=4

:: 
:: package all the output from php sdk source tree to release format (as bellow)
::
:: |--Aliyun
:: |--docs
:: |--sample
:: |--test
:: |--License.txt
:: |--Readme.txt
:: |--Log_Autoload.php
:: |--build.md
::
:: Usage: build.bat 
::
:: Notes: this script must be run under root folder of php SDK code base.

::prepare environment variables
SET SOURCE_ROOT=Aliyun
SET DOCS_ROOT=docs
SET SAMPLE_ROOT=sample
SET TEST_ROOT=test
SET TITLE=SLS_PHP_SDK
SET PACKAGE_NAME=SLS_PHP_SDK

::STEP-1: get current version information from source codes
SET VERSION_FILE=Log_Autoload.php

IF NOT EXIST "%VERSION_FILE%" (
    ECHO Failed to file %VERSION_FILE% on current directory.
    EXIT /B %ERROR_NO_SETUP_FILE%
)

SET SDK_VERSION=
FOR /f "tokens=1-3 delims= " %%G in (%VERSION_FILE%) do (
    IF "%%G"=="$version" (
        IF "%%H"=="=" (
            SET SDK_VERSION=%%I
        )
    )
)

::like $version = '0.4.4';
::trim spaces and ;
SET SDK_VERSION=%SDK_VERSION:'=%
SET SDK_VERSION=%SDK_VERSION:;=%

IF "SDK_VERSION"=="" (
    ECHO Failed to get version information from source codes... 
    EXIT /B %ERROR_NO_VERSION%
)

::SETP-2: build documentation from latest php source codes by "phpDocumentor.phar" tool
SET PROD_VERSION=%SDK_VERSION:~0,3%
SET DOCS_AUTO_GENERATION_DIR=%DOCS_ROOT%\_build\html

ECHO %PROD_VERSION%

php tools\phpDocumentor.phar --title="%TITLE%" --defaultpackagename="%PACKAGE_NAME%" --template="responsive-twig" -d %SOURCE_ROOT% -t %DOCS_ROOT%

IF NOT EXIST "%DOCS_ROOT%"\index.html (
    ECHO Failed to generate rst files for documentation.
    EXIT /B %ERROR_NO_DOCS%
)

SET DOCS_AUTO_GENERATION_DIR=docs

::SETP-3: package all the files into output directory
SET OUTPUT_FOLDER=.\build\%SDK_VERSION%
SET OUTPUT_SOURCE_FOLDER=%OUTPUT_FOLDER%\%SOURCE_ROOT%
SET OUTPUT_DOCS_FOLDER=%OUTPUT_FOLDER%\%DOCS_ROOT%
SET OUTPUT_SAMPLE_FOLDER=%OUTPUT_FOLDER%\%SAMPLE_ROOT%
SET OUTPUT_API_REF_FOLDER=%OUTPUT_FOLDER%_API
SET OUTPUT_TEST_FOLDER=%OUTPUT_FOLDER%_TEST

IF EXIST "%OUTPUT_FOLDER%" RMDIR /Q /S "%OUTPUT_FOLDER%"
MKDIR "%OUTPUT_FOLDER%"

:: ROBOCOPY return code has special definition, please check http://ss64.com/nt/robocopy-exit.html
ROBOCOPY ".\%SOURCE_ROOT%" "%OUTPUT_SOURCE_FOLDER%" *.php /S /NFL /NDL
IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )

ROBOCOPY ".\%DOCS_AUTO_GENERATION_DIR%" "%OUTPUT_DOCS_FOLDER%" /S /NFL /NDL /XD .doctrees
IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )

ROBOCOPY ".\%SAMPLE_ROOT%" "%OUTPUT_SAMPLE_FOLDER%" /S /NFL /NDL
IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )

COPY /Y ".\*.txt" "%OUTPUT_FOLDER%"
COPY /Y ".\*.php" "%OUTPUT_FOLDER%"

IF EXIST "%OUTPUT_API_REF_FOLDER%" RMDIR /Q /S "%OUTPUT_API_REF_FOLDER%"
MKDIR "%OUTPUT_API_REF_FOLDER%"

:: ROBOCOPY return code has special definition, please check http://ss64.com/nt/robocopy-exit.html
ROBOCOPY ".\%DOCS_AUTO_GENERATION_DIR%" "%OUTPUT_API_REF_FOLDER%" /S /NFL /NDL /XD .doctrees
IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )

IF EXIST "%OUTPUT_TEST_FOLDER%" RMDIR /Q /S "%OUTPUT_TEST_FOLDER%"
MKDIR "%OUTPUT_TEST_FOLDER%"

:: ROBOCOPY return code has special definition, please check http://ss64.com/nt/robocopy-exit.html
ROBOCOPY ".\%TEST_ROOT%" "%OUTPUT_TEST_FOLDER%" /S /NFL /NDL
IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )

:END
ECHO on
:: exit script with successful exit code
@EXIT /B %SUCCESS_RET%
