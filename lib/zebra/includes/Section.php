<?php

/**
 *  Class for hidden controls.
 *
 *  @author     Stefan Gabos <contact@stefangabos.ro>
 *  @copyright  (c) 2006 - 2012 Stefan Gabos
 *  @package    Controls
 */
class Zebra_Form_Section extends Zebra_Form_Control
{

    /**
     *  Adds an <input type="hidden"> control to the form.
     *
     *  <b>Do not instantiate this class directly! Use the {@link Zebra_Form::add() add()} method instead!</b>
     *
     *  <code>
     *  // create a new form
     *  $form = new Zebra_Form('my_form');
     *
     *  // add a hidden control to the form
     *  // the "&" symbol is there so that $obj will be a reference to the object in PHP 4
     *  // for PHP 5+ there is no need for it
     *  $obj = &$form->add('hidden', 'my_hidden', 'Secret value');
     *
     *  // don't forget to always call this method before rendering the form
     *  if ($form->validate()) {
     *      // put code here
     *  }
     *
     *  // output the form using an automatically generated template
     *  $form->render();
     *  </code>
     *
     *  @param  string  $id             Unique name to identify the control in the form.
     *
     *                                  The control's <b>name</b> attribute will be the same as the <b>id</b> attribute!
     *
     *                                  This is the name to be used when referring to the control's value in the
     *                                  POST/GET superglobals, after the form is submitted.
     *
     *                                  <b>Hidden controls are automatically rendered when the {@link Zebra_Form::render() render()}
     *                                  method is called!</b><br>
     *                                  <b>Do not print them in template files!</b>
     *
     *  @param  string  $default        (Optional) Default value of the text box.
     *
     *  @return void
     */
    function Zebra_Form_Section($id, $attributes = array())
    {

        // call the constructor of the parent class
        parent::Zebra_Form_Control();

        // set the private attributes of this control
        // these attributes are private for this control and are for internal use only
        // and will not be rendered by the _render_attributes() method
        $this->private_attributes = array(
            'disable_xss_filters',
            'end',
            'locked',
            'name',
            'repeatable',
            'type',
        );

        // set the default attributes for the hidden control
        // put them in the order you'd like them rendered

        // notice that if control's name is 'MAX_FILE_SIZE' we'll generate a random ID attribute for the control
        // as, with multiple forms having upload controls on them, this hidden control appears as many times as the
        // forms do and we don't want to have the same ID assigned to multiple controls
        $this->set_attributes(

            array(

                'class'         =>  'zf_section',
                'end'           =>  false,
                'id'            =>  $id . (isset($attributes['end']) && $attributes['end'] === true ? '_end' : ''),
                'name'          =>  $id . (isset($attributes['end']) && $attributes['end'] === true ? '_end' : ''),
                'repeatable'    =>  false,
                'type'          =>  'section',

            )

        );

        // sets user specified attributes for the control
        $this->set_attributes($attributes);

    }

    /**
     *  Generates the control's HTML code.
     *
     *  <i>This method is automatically called by the {@link Zebra_Form::render() render()} method!</i>
     *
     *  @return string  The control's HTML code
     */
    function toHTML()
    {

//         print_r('<pre>');
//         print_r($this->attributes);
//         die();

        if ($this->attributes['end']) return '</div>';

        return '<div ' . $this->_render_attributes() . '>';

    }

}

?>
