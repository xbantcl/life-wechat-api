<?php namespace Dolphin\Ting\Http\Validation;

use Dolphin\Ting\Http\Response\ServiceResponse;
use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Dolphin\Ting\Http\Utils\Help;

/**
 *
 */
class Validator
{
    protected $errors;

    public function validate($request, array $rules)
    {
        $basicRules = [
            'sys_p' => v::noWhitespace()->notEmpty(), // 系统平台.
            'sys_v' => v::noWhitespace()->notEmpty(), // 系统版本号.
            'sys_d' => v::noWhitespace()->notEmpty(), // 用户设备号.
            'sys_m' => v::notEmpty(), // 用户手机型号.
            'cli_v' => v::noWhitespace()->notEmpty(), // 客户端版本号.
            'cli_p' => v::noWhitespace()->notEmpty(), // 客户端平台.
        ];
        //$rules = array_merge($rules, $basicRules);
        $params = Help::getParams($request);
        foreach ($rules as $field => $rule) {
            try {
                $val = isset($params[$field]) ? $params[$field] : '';
                $rule->setName(ucfirst($field))->assert($val);
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }
        return $this;
    }

    public function failed()
    {
        return !empty($this->errors);
    }

    public function outputError($response)
    {
        if ($this->failed()) {
            return new ServiceResponse(
                [],
                -1,
                lcfirst(current(current($this->errors)))
            );
        }
    }
}
