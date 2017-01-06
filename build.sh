echo " Start to package SLS php sdk..."

export SUCCESS_RET=0
export ERROR_NO_SETUP_FILE=1
export ERROR_NO_VERSION=2
export ERROR_NO_DOCS=3
export ERROR_ROBOCOPY=4

#:: 
#:: package all the output from php sdk source tree to release format (as bellow)
#::
#:: |--Aliyun
#:: |--docs
#:: |--sample
#:: |--test
#:: |--License.txt
#:: |--Readme.txt
#:: |--Log_Autoload.php
#:: |--build.md
#::
#:: Usage: build.bat 
#::
#:: Notes: this script must be run under root folder of php SDK code base.

#::prepare environment variables
export SOURCE_ROOT=Aliyun
export DOCS_ROOT=docs
export SAMPLE_ROOT=sample
export TEST_ROOT=test
export TITLE=LOG_PHP_SDK
export PACKAGE_NAME=LOG_PHP_SDK

#::STEP-1: get current version information from source codes
export VERSION_FILE=Log_Autoload.php

if [ ! -f $VERSION_FILE ]; then
    echo 'Failed to file $VERSION_FILE on current directory.'
    exit 0
fi
SDK_VERSION=`grep '^\$version' $VERSION_FILE|cut -d "'" -f 2`

#::like $version = '0.4.4';
#::trim spaces and ;

if [ "$SDK_VERSION" == "" ]; then
    echo "Failed to get version information from source codes..."
    exit
fi

#::SETP-2: build documentation from latest php source codes by "phpDocumentor.phar" tool
export PROD_VERSION=${SDK_VERSION:0:3}
export DOCS_AUTO_GENERATION_DIR=$DOCS_ROOT/_build/html

echo $PROD_VERSION

php tools/phpDocumentor.phar --title="$TITLE" --defaultpackagename="$PACKAGE_NAME" --template="responsive-twig" -d $SOURCE_ROOT -t $DOCS_ROOT

if [ ! -f $DOCS_ROOT/index.html ]; then
    echo "Failed to generate rst files for documentation."
    exit 0
fi

export DOCS_AUTO_GENERATION_DIR=docs

#::SETP-3: package all the files into output directory
export OUTPUT_FOLDER=./build/$SDK_VERSION
export OUTPUT_SOURCE_FOLDER=$OUTPUT_FOLDER/$SOURCE_ROOT
export OUTPUT_DOCS_FOLDER=$OUTPUT_FOLDER/$DOCS_ROOT
export OUTPUT_SAMPLE_FOLDER=$OUTPUT_FOLDER/$SAMPLE_ROOT
export OUTPUT_API_REF_FOLDER=${OUTPUT_FOLDER}_API
export OUTPUT_TEST_FOLDER=${OUTPUT_FOLDER}_TEST

if [ -d $OUTPUT_FOLDER ]; then
    rm -rf $OUTPUT_FOLDER
    mkdir $OUTPUT_FOLDER
fi

#:: ROBOCOPY return code has special definition, please check http://ss64.com/nt/robocopy-exit.html
find $SOURCE_ROOT/* -type f | grep \.php$ | cpio -pd $OUTPUT_SOURCE_FOLDER
#ROBOCOPY ".\%SOURCE_ROOT%" "%OUTPUT_SOURCE_FOLDER%" *.php /S /NFL /NDL

find ./$DOCS_AUTO_GENERATION_DIR   -type f |grep .doctrees |cpio -pd $OUTPUT_DOCS_FOLDER
#ROBOCOPY ".\%DOCS_AUTO_GENERATION_DIR%" "%OUTPUT_DOCS_FOLDER%" /S /NFL /NDL /XD .doctrees
#IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )

cp -r ./$SAMPLE_ROOT  $OUTPUT_SAMPLE_FOLDER
#ROBOCOPY ".\%SAMPLE_ROOT%" "%OUTPUT_SAMPLE_FOLDER%" /S /NFL /NDL
#IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )
cp -f ./*.txt $OUTPUT_FOLDER
cp -f ./*.php $OUTPUT_FOLDER
#COPY /Y ".\*.txt" "$OUTPUT_FOLDER"
#COPY /Y ".\*.php" "$OUTPUT_FOLDER"

if [ -d $OUTPUT_API_REF_FOLDER ];  then
    rm -rf $OUTPUT_API_REF_FOLDER
    mkdir $OUTPUT_API_REF_FOLDER
fi
#IF EXIST "%OUTPUT_API_REF_FOLDER%" RMDIR /Q /S "%OUTPUT_API_REF_FOLDER%"
#MKDIR "%OUTPUT_API_REF_FOLDER%"

#:: ROBOCOPY return code has special definition, please check http://ss64.com/nt/robocopy-exit.html
find $DOCS_AUTO_GENERATION_DIR/* -type f | grep .doctrees|cpio -pd $OUTPUT_API_REF_FOLDER
#ROBOCOPY ".\%DOCS_AUTO_GENERATION_DIR%" "%OUTPUT_API_REF_FOLDER%" /S /NFL /NDL /XD .doctrees

if [ -d $OUTPUT_TEST_FOLDER ]; then
    rm -rf $OUTPUT_TEST_FOLDER
    mkdir $OUTPUT_TEST_FOLDER
fi
#IF EXIST "%OUTPUT_TEST_FOLDER%" RMDIR /Q /S "%OUTPUT_TEST_FOLDER%"
#MKDIR "%OUTPUT_TEST_FOLDER%"

##:: ROBOCOPY return code has special definition, please check http://ss64.com/nt/robocopy-exit.html
cp -r $TEST_ROOT $OUTPUT_TEST_FOLDER 
#ROBOCOPY ".\%TEST_ROOT%" "%OUTPUT_TEST_FOLDER%" /S /NFL /NDL
#IF %ERRORLEVEL% GTR 1 ( EXIT /B %ERROR_ROBOCOPY% )
exit 0
