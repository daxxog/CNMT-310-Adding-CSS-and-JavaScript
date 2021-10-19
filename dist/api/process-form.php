<?php
header('Content-Type: application/json; charset=utf-8');
$required_elements = ['username', 'password', 'confirmPassword', 'emailAddress'];

foreach($required_elements as $element){
    if( (!isset($_POST[$element])) || empty($_POST[$element]) ) {
        http_response_code(400);
        die(print('"bad request"'));
    }
}

// str_contains polyfill for PHP7
function _str_contains($haystack, $needle) {
    if(strpos($haystack, $needle) !== false) {
        return true;
    } else {
        return false;
    }
}

function findCharType($char) {
    // string constants exported from python
    $ASCII_LOWERCASE = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
    $ASCII_UPPERCASE = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
    $DIGITS = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
    $PUNCTUATION = ["!", "\"", "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "\\", "]", "^", "_", "`", "{", "|", "}", "~"];

    $CHARTYPE_ASCII_LOWERCASE = 0;
    $CHARTYPE_ASCII_UPPERCASE = 1;
    $CHARTYPE_DIGITS = 2;
    $CHARTYPE_PUNCTUATION = 3;
    $CHARTYPE_OTHER = 4;

    if(in_array($char, $ASCII_LOWERCASE)) {
        return $CHARTYPE_ASCII_LOWERCASE;
    } else if (in_array($char, $ASCII_UPPERCASE)) {
        return $CHARTYPE_ASCII_UPPERCASE;
    } else if (in_array($char, $DIGITS)) {
        return $CHARTYPE_DIGITS;
    } else if (in_array($char, $PUNCTUATION)) {
        return $CHARTYPE_PUNCTUATION;
    } else {
        return $CHARTYPE_OTHER;
    }
}

function dieBecause($element) {
    http_response_code(400);
    die(print("{\"display_error\": \"Server side validation: invalid input for element --> ${element}\"}"));
}

// could do one loop, but using two loops to "weed out" empty input first
foreach($required_elements as $element){
    switch ($element) {
        case 'username':
            if(! (strlen($_POST[$element]) >= 3) ) {
                dieBecause($element);
            }
            break;
        case 'emailAddress':
            if(!filter_var($_POST[$element], FILTER_VALIDATE_EMAIL)) {
                dieBecause($element);
            }
            break;
        case 'password':
            if(! (strlen($_POST[$element]) >= 32) ) {
                dieBecause($element);
            }

            $passwordChars = str_split($_POST[$element]);
            $lastCharType = -1;
            $nextCharType = -1;

            foreach($passwordChars as $char) {
                $nextCharType = findCharType($char);
                if($nextCharType === $lastCharType) {
                    dieBecause($element);
                }
                $lastCharType = $nextCharType;
            }
            break;
        case 'confirmPassword':
            if(! ($_POST[$element] === $_POST['password'])) {
                dieBecause($element);
            }
            break;
    }
}

// if we didn't die at this point, everything passed our validation
http_response_code(200);
print '"good request"';
?>
