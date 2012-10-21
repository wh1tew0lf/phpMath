<?php
/**
 * Interface for Single Numbers (ints floats and the same)
 * @author Danil Volkov <volkovdanil91@gmail.com>
 * @package phpMath
 */
interface ISingle extends INumber {

    /**
     * Creates new example of current class
     * @param mixed $value
     * @return self self object
     */
    public static function create($value);

}