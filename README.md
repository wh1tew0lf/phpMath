phpMath
=======
There are some classes and interfases for make calculates in php. It need for my
course work in university. I understand that may be C/C++ much better for
computing, but it only protoptype, maybe in future I transfer it to C++ or lisp.
Also there are my examples of code style and system design.

Sorry if I take name of the existing package. But I do not find anything before
I start write. All that I wrote self. You can write me by mail:
volkovdanil91@gmail.com

Some description of classes and interfases:
*Interfases:
    1) INumber - base interfase for all classes.
    2) ISingle - interfase for Single classes like integer.
    3) IComposite - interfase for Composite classes like fractions, complex and
    etc.
*Classes:
    1) CInteger - class for integers (GMP numbers).
    2) CFraction - fractions class, for all calculations.
    3) CVector - array of the some elements.
    4) CMatrix - CVector of CVector. But it must be rectangle (Vector can be as
staircase)
    5) CSolver - class for some alghoritms.

This is not a complete project, I am still working on it.
More problems now with create method of ISingle and IComposite. Also CVector and
CMatrix mul. So I think how do it all better.

If you have some ideas of criticism you can write me volkovdanil91@gmail.com
Please write on the subject of mail - "phpMath"
Regards,
Danil