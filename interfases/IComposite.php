<?php
/**
 * Interface for Composite Numbers (Fractions, Complex Numbers)
 * @author Danil Volkov <volkovdanil91@gmail.com>
 * @package phpMath
 */
interface IComposite extends INumber {

    /**
     * Creates new example of current class
     * @param type $hierarhy
     * @param mixed $values
     * @return self self object
     */
    public static function create($hierarhy, $values);

    /**
     * Returns hierarchy of this object
     * @return string
     */
    public function getHierarchy();

    /**
     * Returns element of this object hierarchy
     * @param string $hierarhy current
     * @param integer $level level of sub
     * @return string
     */
    public function getSubHierarchy($hierarhy = '', $level = 1);
}
?>
