<?php

namespace engine\utils;

use engine\enums\Constants;
use engine\security\Escape;
use engine\storage\Storage;
use WP_Error;

defined('ABSPATH') || exit;

class Validator
{
    public static function checkInputLength(string $fieldName, string $input, string $dataType, string $rules, WP_Error &$errors): bool
    {
        if ($dataType == 'number')
        {
            if (is_numeric($input))
            {
                $checkRules = self::extractRules($rules);

                if (mb_strlen($input,'UTF-8') <= $checkRules['max'] &&
                    mb_strlen($input,'UTF-8') >= $checkRules['min'])
                {
                    return true;
                }

                else
                {
                    if ($checkRules['max'] == $checkRules['min'])
                    {
                        $errors->add(
                            $fieldName,
                            Escape::htmlWithTranslation(
                                sprintf(
                                    'تعداد ارقام فیلد %s باید %s باشد',
                                    $fieldName,
                                    $checkRules['max'],
                                )
                            )
                        );
                    }

                    else
                    {
                        $errors->add(
                            $fieldName,
                            Escape::htmlWithTranslation(
                                sprintf(
                                    'تعداد ارقام فیلد %s باید بین %s و %s باشد',
                                    $fieldName,
                                    $checkRules['max'],
                                    $checkRules['min']
                                )
                            )
                        );
                    }

                    return false;
                }
            }

            else
            {
                $errors->add(
                    $fieldName,
                    Escape::htmlWithTranslation(
                        sprintf(
                            'مقدار فیلد %s باید عدد باشد',
                            $fieldName,
                        )
                    )
                );

                return false;
            }
        }

        else if ($dataType == 'text')
        {
            // making sure whole text is not numbers!
            if (!is_numeric($input))
            {
                $checkRules = self::extractRules($rules);

                if (mb_strlen($input,'UTF-8') <= $checkRules['max'] &&
                    mb_strlen($input,'UTF-8') >= $checkRules['min'])
                {
                    return true;
                }

                else
                {
                    $errors->add(
                        $fieldName,
                        Escape::htmlWithTranslation(
                            sprintf(
                                'تعداد حروف فیلد %s باید بین %s و %s باشد',
                                $fieldName,
                                $checkRules['max'],
                                $checkRules['min']
                            )
                        )
                    );

                    return false;
                }
            }

            else
            {
                $errors->add(
                    $fieldName,
                    Escape::htmlWithTranslation(
                        sprintf(
                            'مقدار فیلد %s نباید کامل از اعداد نشکیل شده باشد',
                            $fieldName,
                        )
                    )
                );

                return false;
            }
        }

        return false;
    }

    public static function checkIntValue(string $fieldName,string $input,string $dataType,string $rules, WP_Error &$errors): bool
    {
        if ($dataType == 'number')
        {
            if (is_numeric($input))
            {
                $checkRules = self::extractRules($rules);

                if (intval($input) <= intval($checkRules['max']) &&
                    intval($input) >= intval($checkRules['min']))
                {
                    return true;
                }

                else
                {
                    $errors->add(
                        $fieldName,
                        Escape::htmlWithTranslation(
                            sprintf(
                                'مقدار فیلد %s باید بین %s و %s باشد',
                                $fieldName,
                                $checkRules['max'],
                                $checkRules['min']
                            )
                        )
                    );

                    return false;
                }
            }

            else
            {
                $errors->add(
                    $fieldName,
                    Escape::htmlWithTranslation(
                        sprintf(
                            'مقدار فیلد باید %s عدد باشد',
                            $fieldName,
                        )
                    )
                );

                return false;
            }
        }

        return false;
    }

    public static function checkEmail(string $fieldName,string $input, WP_Error &$errors): bool
    {
        if (!is_numeric($input) && is_email($input))
        {
            return true;
        }

        else
        {
            $errors->add(
                $fieldName,
                Escape::htmlWithTranslation(
                    sprintf(
                        'مقدار فیلد %s حاوی ایمیل معتبری نیست',
                        $fieldName,
                    )
                )
            );

            return false;
        }
    }

    public static function checkGender(string $fieldName,string $input, WP_Error &$errors): bool
    {
        $gender = match ($input)
        {
            'male', 'female' => true,
            default => false,
        };

        if (!$gender)
        {
            $errors->add(
                $fieldName,
                Escape::htmlWithTranslation(
                    sprintf(
                        '%s %s تعریف نشده!',
                        $fieldName,
                        $input,
                    )
                )
            );
        }

        return $gender;
    }

    public static function checkState(string $fieldName,string $input, WP_Error &$errors): bool
    {
        $states = Storage::getJsonContent(Constants::Storage.'/json/states.json');

        if (!isset($states[$input]))
        {
            $errors->add(
                $fieldName,
                Escape::htmlWithTranslation(
                    sprintf(
                        '%s معتبر نیست',
                        $fieldName,
                    )
                )
            );

            return false;
        }

        return true;
    }

    public static function checkCity(string $fieldName,string $input, WP_Error &$errors): bool
    {
        $cities = Storage::getJsonContent(Constants::Storage.'/json/cities.json');

        $request = Request::post();

        if (!($request->has('state') && isset($cities[$request->getParam('state')][$input])))
        {
            $errors->add(
                $fieldName,
                Escape::htmlWithTranslation(
                    sprintf(
                        '%s معتبر نیست',
                        $fieldName,
                    )
                )
            );

            return false;
        }

        return true;
    }

    /**
     * Extracts validation rules
     *
     * @param string $rules
     * @return array
     */
    private static function extractRules(string $rules): array
    {
        $rules = explode('|', $rules);

        $result = [];

        foreach ($rules as $rule)
        {
            list($key, $value) = explode(':', $rule);
            $result[$key] = $value;
        }

        return $result;
    }
}