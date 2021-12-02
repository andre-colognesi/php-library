<?php
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

function renderInput($name, $label = null, $props = array(), $classes = array(), $errors = array())
{
    $input = '';
    $prop = '';
    foreach ($props as $key => $value) {
        if ($value) {
            $prop .= sprintf('%s="%s"', $key, $value);
            $prop .= " ";
        }
    }

    if ($label) {
        $input.= sprintf("<label for='%s'>%s</label>", $name, $label);
    }


        $errorDiv = "<div id='".$name."_invalid_container' class='invalid-feedback text-center'>";
    if (count($errors) > 0) {
        array_push($classes, 'is-invalid');
        foreach ($errors as $error) {
            $errorDiv .= $error . "<br>";
        }
    }
    $errorDiv.= "</div>";
    $input.= sprintf("<input name='%s' class='%s' %s /> %s<br>", $name, join(" ", $classes), $prop, $errorDiv);

    echo $input ;
}

function renderFilterButtons($newUrl = null, $previousUrl = null, $showNew = true, $newLabel = 'Novo')
{
    $newBtn = '';
    if (app('CheckPermission')->can('C')) {
        if (!$newUrl) {
            $newUrl = url()->current()."/novo";
        }
        if ($showNew) {
            $newBtn = sprintf('<a class="btn btn-success" href="%s">%s</a>', $newUrl,$newLabel);
        }
    }
    if (!$previousUrl) {
        $previousUrl = url()->previous();
    }
    $panel = sprintf(
        '<div class="container-fluid border rounded-lg shadow-sm mt-3 p-3 bg-light ">
    
    %s
    </div><hr>', $newBtn
    );
    echo $panel;
}

function renderFilterButtons2_0($newUrl = null, $previousUrl = null, $showNew = true)
{
    $newBtn = '';
    if (app('CheckPermission')->can('C')) {
        if (!$newUrl) {
            $newUrl = url()->current()."/novo";
        }
        if ($showNew) {
            $newBtn = sprintf('<a class="btn btn-success" href="%s">Novo</a>', $newUrl);
        }
    }
    if (!$previousUrl) {
        $previousUrl = url()->previous();
    }
    $panel = sprintf(
        '<div class="row">
        <div class="col-sm-3">
    %s
    </div>
<div class="col-sm-12"><hr></div>
    </div>', $newBtn
    );
    echo $panel;
}

function renderFormButtons($previousUrl = null, array $attr = array(), $btnValue = 'Salvar')
{
    $updateBtn = '';
    $activateBtn = '';
    if (app('CheckPermission')->can('U')) {
        $updateBtn .= '<input type="submit" value="'.$btnValue.'" class="btn btn-success submit-form-button"/>';
    }
    if (!$previousUrl) {
        $previousUrl = url()->previous();
    }
    $panel = sprintf(
        '<div class="%s">
    %s
    <a class="btn btn-warning" href="%s">Voltar</a>
    %s
    </div>', join(' ', $attr), $updateBtn, $previousUrl, $activateBtn
    );
    echo $panel;
}


function renderSelect($name, $label = false, $items, string $key, string $value, array $extra = array(), $classes = array(), $oldValue = null, $errors = array())
{
    $lbl = '';
    if ($label) {
        $lbl.= sprintf("<label for='%s'>%s</label>", $name, $label);
    }
    $options = '';
    if (is_object($items)) {
        foreach ($items as $item) {
            $selected = '';
            if ($oldValue && $oldValue == $item->$key) {
                $selected = 'selected="true"';
            }
            $options .= sprintf("<option %s value='%s'>%s</option>", $selected, $item->$key, $item->$value);
        }
    }
    if (is_array($items)) {
        foreach ($items as $item) {
            $item = (array)$item;
            $selected = '';
            if ($oldValue && $oldValue == $item[$key]) {
                $selected = 'selected="true"';
            }
            $options .= sprintf("<option %s value='%s'>%s</option>", $selected, $item[$key], $item[$value]);
        }
    }
    $errorDiv = '';

        $errorDiv .= "<div  id='".$name."_invalid_container' class='invalid-feedback text-center'>";
    if (count($errors) > 0) {
        array_push($classes, 'is-invalid');
        foreach ($errors as $error) {
            $errorDiv .= $error . "<br>";
        }
    }
    $errorDiv.= "</div>";
    $select = sprintf('%s<select name="%s" %s class="%s"><option value="">Selecione</option>%s</select>%s<br>', $lbl, $name, join(" ", $extra), join(' ', $classes), $options, $errorDiv);
    echo $select;
}

function renderSimpleSelect($name, $label = false, $options, $class = [], $extra = [], $oldVal = false, $errors = [])
{
    $lbl = '';
    if ($label) {
        $lbl.= sprintf("<label for='%s'>%s</label>", $name, $label);
    }
    $opt = '<option value="">Selecione</option>';
    foreach ($options as $k => $v) {
        $selected = '';
        if ($oldVal && $oldVal == $k) {
            $selected = 'selected="true"';
        }
        $opt .= "<option $selected value='$k'>$v</option>";
    }
    $errorDiv = '';
    $errorDiv .= "<div class='invalid-feedback text-center'>";
    if (count($errors) > 0) {
        array_push($class, 'is-invalid');
        foreach ($errors as $error) {
            $errorDiv .= $error . "<br>";
        }
    }
        $errorDiv.= "</div>";
    $select = sprintf('<select %s class="%s" name="%s">%s</select>', join(" ", $extra), join(" ", $class), $name, $opt);
    echo $lbl . $select . $errorDiv . '<br>';
}

function renderRadio($name, $id, $label = false, array $values, $oldVal = null, array $errors = array())
{
    $radio = '';
    if ($label) {
        $radio = sprintf("<label for='%s'>%s</label><br>", $name, $label);
    }
    $hasError = null;
    if (count($errors) > 0) {
        $hasError = 'is-invalid';
    }
    $counter = 1;
    foreach ($values as $key => $value) {
        $checked = '';
        if (($oldVal) && $key == $oldVal) {
            $checked = 'checked="true"';
        }
        $radioId = sprintf("%s-%s", $id, $counter);
        $radio .= sprintf(
            '<div class="form-check form-check-inline custom-radio">
        <input class="form-check-input custom-control-input %s" %s type="radio" name="%s" id="%s" value="%s">
        <label class="form-check-label custom-control-label" for="%s">%s</label></div>', $hasError, $checked, $name, $radioId, $key, $radioId, $value
        );
        $counter++;
    }
    $errorDiv = '';
    $errorDiv .= sprintf("<div class='invalid-feedback text-center' %s>", $hasError ? 'style="display:block"' : '');
    if ($hasError) {
        foreach ($errors as $error) {
            $errorDiv .= $error . "<br>";
        }
    }
    $errorDiv.= "</div>";
    $radio.= $errorDiv . '<br>';
    echo $radio;
}

function renderTitle($title, $class = array(), $extra = array(),$size = '3')
{
    $header = sprintf(
        '<div class="row %s">
    <div class="col-sm-6">
    <h'.$size.'><b>%s</b></h'.$size.'>
    </div>
    %s
<div class="col-sm-12"><hr></div>
</div>', join(" ", $class), $title, join(" ", $extra)
    );
    echo $header;
}

function renderIputButton($name, $appendButton = [], $label = null, $props = array(), $classes = array(), $errors = array())
{
    $input = '';
    $prop = '';
    $button = sprintf('<a class="%s" %s >%s</a>',isset($appendButton['class']) ? $appendButton['class'] : '',
    isset($appendButton['props']) ? join(" ",$appendButton['props']) : '', $appendButton['title']
);
    foreach ($props as $key => $value) {
        if ($value) {
            $prop .= sprintf('%s="%s"', $key, $value);
            $prop .= " ";
        }
    }

    if ($label) {
        $input.= sprintf("<label for='%s'>%s</label>", $name, $label);
    }
    
    $errorDiv = "<div class='invalid-feedback text-center'>";
    if (count($errors) > 0) {
        array_push($classes, 'is-invalid');
        foreach ($errors as $error) {
            $errorDiv .= $error . "<br>";
        }
    }

    $errorDiv.= "</div>";
    $input.= sprintf("<div class='input-group'><input name='%s' class='%s' %s /><div class='input-group-append'>%s</div> %s<br>",
     $name, join(" ", $classes), $prop,$button, $errorDiv);
    echo $input ;
}

function renderInputGroup($name, $prependValue, $label = null, $props = array(), $classes = array(), $errors = array())
{

    $input = '';
    $prop = '';
    foreach ($props as $key => $value) {
        if ($value) {
            $prop .= sprintf('%s="%s"', $key, $value);
            $prop .= " ";
        }
    }

    if ($label) {
        $input.= sprintf("<label for='%s'>%s</label>", $name, $label);
    }
    
    $errorDiv = "<div class='invalid-feedback text-center'>";
    if (count($errors) > 0) {
        array_push($classes, 'is-invalid');
        foreach ($errors as $error) {
            $errorDiv .= $error . "<br>";
        }
    }

    $errorDiv.= "</div>";
    $input.= sprintf("<div class='input-group'><div class='input-group-prepend'><span class='input-group-text' id='%s'>%s</span></div><input name='%s' class='%s' %s /></div> %s<br>", $name, $prependValue, $name, join(" ", $classes), $prop, $errorDiv);
    echo $input ;
}

function renderSwitch($name, $label, $extra = array(), $size = 'custom-switch-md')
{
    printf(
        '<div class="custom-control custom-switch %s">
            <input id="%s" type="checkbox" name="%s" %s class="custom-control-input">
            <label class="custom-control-label" for="%s">%s</label>
          </div>', $size, $name, $name, join(" ", $extra), $name, $label
    );
}

function renderTextArea($name, $label = false, $value = null, $extra = [], $classes = [], $errors = null)
{
    $input = '';
    if ($label) {
        $input.= sprintf("<label for='%s'>%s</label>", $name, $label);
    }
        $errorDiv = "<div class='invalid-feedback text-center'>";
    if ($errors && count($errors) > 0) {
        array_push($classes, 'is-invalid');
        foreach ($errors as $error) {
            $errorDiv .= $error . "<br>";
        }
    }
    $errorDiv.= "</div>";
    $input.= sprintf("<textarea name='%s' %s class='%s'>%s</textarea> %s<br>", $name, join(' ', $extra), join(' ', $classes), $value, $errorDiv);
    echo $input ;
}

function renderSearchFormButtons($extra = array())
{
    $input = sprintf(
        '<div class="row">
    <div class="col-sm-12">
    <button type="submit" class="btn btn-primary">Buscar <small><i class="fas fa-search"></i></small></button>
    <a class="btn btn-secondary " href="%s">Limpar</a>
    %s
</div>
</div>', Request::url(), join(" ", $extra)
    );
    echo $input;
}

function formatDate($date)
{
    $date = new DateTime($date);
    return $date->format('d/m/Y H:i:s');
}

function renderAjaxDivList($listProps = [], $tableProps = [], $pagesProp = [], $buttons = [],$title = null)
{

    if($title){
        $title = '<div class="row"><div class="col-sm-12"><h3>'.$title.'</h3></div><div class="col-sm-12"><hr></div></div>';
    }
    if (!isset($tableProps['id'])) {
        $tableProps['id'] = 'pagination-list';
    }
    $buttonsRow = '';
    $divWrapper = '';

    if(!count($buttons)){
        $buttons = renderListButtons();
    }
    foreach($buttons as $div){
        if($div){
        $divWrapper = "<div class=\"col-sm-{$div['size']}\">";

        foreach($div['buttons'] as $button){
            $formattedButtons = '';
            
            $formattedButtons .= sprintf('<a class="%s" %s id="%s" name="%s" %s >%s</a>',$button['class'] ?? '', 
            isset($button['href']) ?  'href="'.$button['href'].'"' : '', $button['id'] ?? '', 
            $button['name'] ?? '',$button['extra']  ?? '',$button['label'] ?? 'Novo');

            $divWrapper .= $formattedButtons;
        }  
        $divWrapper .= '</div>';
        $buttonsRow .= $divWrapper;
    }
     }
    $div = sprintf(
        '<div %s class="%s container-fluid border table-responsive rounded-lg shadow mt-2 p-3 bg-light mb-4">
        %s
        <div class="row mt-1">
        %s
        <div class="col-sm-12"><hr></div>
        </div>
        <div class="table-responsive">
        <table id="%s" class="%s table table-sm table-hover">

        </table>
    </div>
    <hr>
        <div class="d-flex justify-content-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination" id="%s">
                </ul>
              </nav>
        </div>
        <span class="font-italic align-top d-flex justify-content-end" id="'.$tableProps['id'].'_total_span"></span>
    </div>',
        isset($listProps['id']) ? "id='{$listProps['id']}'" : '',
        isset($listProps['class']) ? $listProps['class'] : '',
        $title,
        $buttonsRow,
        $tableProps['id'],
        isset($tableProps['class']) ? $tableProps['class'] : '',
        isset($pagesProp['id']) ? $pagesProp['id'] : 'pages-div'
    );

    echo $div;
}

function renderInputHidden($name,$props)
{
    $prop = '';
    foreach ($props as $key => $value) {
        if ($value) {
            $prop .= sprintf('%s="%s"', $key, $value);
            $prop .= " ";
        }
    }

    $input = "<input name=\"{$name}\" type=\"hidden\" {$prop} />";
    echo $input;
}

function renderListButtons($buttons = [])
{
    if(isset($buttons['show']) && $buttons['show'] == false){
        return null;
    }   
    if(app('CheckPermission')->can('C')){
       $default = [
            //div wrapper
            'size' => '2',
            'buttons' => [
                ['label' => 'Novo',
            'class' => 'btn btn-success',
            'href' => url()->current() . '/novo',
            'id' => '',
            'name' => '',
            'extra' => '']
           ]
        ];
        }
    if(empty($buttons)){
        return [
            $default
        ];

        return $buttons;
    }
}


function renderCheckbox($name,$label,$value = null,$extra = [])
{
   $checkbox = '';
    $checkbox .= sprintf("<div class=\"form-check custom-control custom-checkbox\"><br>
    <input type=\"checkbox\"  name=\"{$name}\" %s  %s class=\"form-check-input custom-control-input\" id=\"{$name}\">
    <label class=\"form-check-label custom-control-label\" for=\"{$name}\">%s</label>
  </div>",join(" ", $extra),$value != null ? 'checked="checked"' : '',$label);

  echo $checkbox;
}

function formatArrayLog(array $array)
{
    $res = '';
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $res .= formatArrayLog($value);
            continue;
        }
            $res.= $key . ": " . $value ."\n";
    }

    return $res;
}

function addZero($value)
{
    if (strlen($value) >= 2) {
        return $value;
    }

    return "0".$value;
}

//facilitador para a função das permissões
function can(string $action,string $module = null)
{
    return app('CheckPermission')->can($action,$module);
}

function any($module)
{
    return app('CheckPermission')->any($module);
}


function add_query_params(array $params = [])
{
    $query = array_merge(
        request()->query(),
        $params
    ); // merge the existing query parameters with the ones we want to add

    return dd(url()->current() . '?' . http_build_query($query)); // rebuild the URL with the new parameters array
}
