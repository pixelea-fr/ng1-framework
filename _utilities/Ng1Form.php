<?php 
class ng1Form {
    private $fields;

    public function __construct($fields) {
        $this->fields = $fields;
    }

    public function generateFormFields() {
        $formFieldsHTML = '<div class="ng1-form__items">';
        foreach ($this->fields as $field) {
            $formFieldsHTML .= '<div class="ng1-form__item">'; // Ouvre la balise div pour chaque champ

            if (isset($field['type'])) {
                $formFieldsHTML .= $this->generateField($field);
            }

            $formFieldsHTML .= '</div>'; // Ferme la balise div pour chaque champ
        }
        $formFieldsHTML .= '</div>'; // Ferme la balise div pour chaque champ
        return $formFieldsHTML;
    }

    private function generateField($field) {
        $inputType = $field['type'];
        switch ($inputType) {
            case 'select':
                if (isset($field['options'])) {
                    return $this->generateSelect($field);
                }
                break;
            case 'textarea':
                return $this->generateTextarea($field);
            case 'radio':
            case 'checkbox':
                return $this->generateCheckboxesOrRadios($field);
            case 'submit':
                return $this->generateSubmitField($field);
            default:
                return $this->generateInputField($field);
        }
    }

    private function generateInputField($field) {
        $inputName = $field['name'];
        $label = $field['label'];
        $attributes = $this->generateAttributes($field);

        return '<label class="ng1-form__label" for="' . $inputName . '">' . $label . ':</label>' .
               '<input class="ng1-form__field" type="' . $field['type'] . '" name="' . $inputName . '" ' . $attributes . '>';
    }
    private function generateSubmitField($field) {
        $inputName = $field['name'];
        $label = $field['label'];
        $attributes = $this->generateAttributes($field);

        return  '<input class="ng1-form__field" type="' . $field['type'] . '" name="' . $inputName . '" ' . $attributes . '>';
    }
    private function generateTextarea($field) {
        $inputName = $field['name'];
        $label = $field['label'];
        $attributes = $this->generateAttributes($field);

        return '<label class="ng1-form__label" for="' . $inputName . '">' . $label . ':</label>' .
               '<textarea class="ng1-form__field" name="' . $inputName . '" ' . $attributes . '></textarea>';
    }

    private function generateCheckboxesOrRadios($field) {
        $inputName = $field['name'];
        $label = $field['label'];
        $options = isset($field['options']) ? $field['options'] : [];

        $optionsHTML = '<p>' . $label . ':</p>';

        foreach ($options as $option) {
            $attributes = $this->generateAttributes($field);
            $optionsHTML .= '<input class="ng1-form__field" type="' . $field['type'] . '" name="' . $inputName . '" value="' . $option . '" ' . $attributes . '>';
            $optionsHTML .= '<label class="ng1-form__label">' . $option . '</label>';
        }

        return $optionsHTML;
    }

    private function generateSelect($field) {
        $inputName = $field['name'];
        $label = $field['label'];
        $attributes = $this->generateAttributes($field);
        $options = isset($field['options']) ? $field['options'] : [];

        $selectHTML = '<label class="ng1-form__label" for="' . $inputName . '">' . $label . ':</label>' .
            '<select class="ng1-form__field" name="' . $inputName . '" ' . $attributes . '>';

        foreach ($options as $option) {
            $selectHTML .= '<option value="' . $option . '">' . $option . '</option>';
        }

        $selectHTML .= '</select>';

        return $selectHTML;
    }

    private function generateAttributes($field) {
        $attributes = [];
        foreach ($field as $attribute => $value) {
            if ($attribute !== 'type' && $attribute !== 'label' && $attribute !== 'options') {
                $attributes[] = $attribute . '="' . $value . '"';
            }
        }
        return implode(' ', $attributes);
    }
}
