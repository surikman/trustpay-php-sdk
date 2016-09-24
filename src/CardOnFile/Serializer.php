<?php
namespace TrustPay\CardOnFile;

class Serializer
{
    private static $storedKeys = [
        'CardID',
        'CardExp',
        'CardMask',
    ];

    /**
     * @param array $data
     *
     * @return string|null
     */
    public function serialize($data)
    {
        $intersectKeys = array_intersect(array_keys($data), static::$storedKeys);
        $token = [];
        foreach ($intersectKeys as $key) {
            $value = $data[$key];

            if (empty($value)) { // all value have to be not empty
                return null;
            }

            $token[] = sprintf("%s:%s", $key, $value);
        }

        return $this->crypt(implode("|", $token));
    }

    /**
     * @param string $data
     *
     * @return array
     */
    public function deserialize($data)
    {
        $token = explode("|", $this->decrypt($data));
        $output = [];
        foreach ($token as $item) {
            list($key, $value) = explode(":", $item);
            $output[$key] = $value;
        }

        return $output;
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function crypt($string)
    {
        $r = [];
        for ($i = 0; $i < strlen($string); $i++) {
            $r[] = chr(ord($string[$i]) + 2);
        }

        return base64_encode(implode('', $r));

    }

    /**
     * @param $string
     *
     * @return string
     */
    private function decrypt($string)
    {
        $string = str_split(base64_decode($string));
        for ($i = 0; $i < count($string); $i++) {
            $string[$i] = chr(ord($string[$i]) - 2);
        }

        return implode('', $string);

    }
}