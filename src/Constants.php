<?php
/**
 * Created by PhpStorm.
 * User: shehin
 * Date: 18/10/19
 * Time: 10:18 PM
 */

namespace shehin\procodervalidator;


class Constants
{
    const EXCEPTION_FILE_NOT_SET = 1;
    const EXCEPTION_COLLECTION_NOT_SET = 2;
    const EXCEPTION_ROW_LIMIT_NOT_SET = 3;
    const EXCEPTION_COLUMN_LIMIT_NOT_SET = 4;
    const EXCEPTION_FILE_EXTENSION_NOT_SET = 5;
    const EXCEPTION_FILE_NAME_NOT_SET = 6;
    const EXCEPTION_EXCEL_NOT_SET = 7;
    const EXCEPTION_IMPORT_NOT_SET = 8;
    const EXCEPTION_FILE_SHOULD_BE_CSV = 9;
    const EXCEPTION_MESSAGE_NOT_SET = 10;
    const EXCEPTION_COLUMNS_NOT_SET = 11;
    const EXCEPTION_INVALID_COLUMNS_NOT_SET = 12;
    const EXCEPTION_ROW_RULE_CUSTOM_MESSAGE_NOT_SET = 13;
    const EXCEPTION_CSV_TERM_MODULE_COLUMNS = 14;

    const MODULE_CODE = 'Module Code';
    const TERM_NAME =   'Term Name';
    const MODULE_NAME = 'Module Name';

    
    const CSV_TERM_MODULE_COLUMNS = [
        Constants::MODULE_CODE,
        Constants::TERM_NAME,
        Constants::MODULE_NAME
    ];

}