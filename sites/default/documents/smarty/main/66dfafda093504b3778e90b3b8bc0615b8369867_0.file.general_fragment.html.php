<?php
/* Smarty version 4.5.5, created on 2025-07-11 09:10:28
  from 'C:\wamp64\www\openemr-7.0.3\templates\prescription\general_fragment.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_6870d5045a9b24_79650950',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '66dfafda093504b3778e90b3b8bc0615b8369867' => 
    array (
      0 => 'C:\\wamp64\\www\\openemr-7.0.3\\templates\\prescription\\general_fragment.html',
      1 => 1752217596,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6870d5045a9b24_79650950 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\wamp64\\www\\openemr-7.0.3\\library\\smarty\\plugins\\function.xlt.php','function'=>'smarty_function_xlt',),));
if (empty($_smarty_tpl->tpl_vars['prescriptions']->value)) {
echo smarty_function_xlt(array('t'=>'None'),$_smarty_tpl);?>

<?php } else { ?>
<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th><?php echo smarty_function_xlt(array('t'=>'Drug'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Details'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Qty'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Refills'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Filled'),$_smarty_tpl);?>
</th>
            </tr>
        </thead>
        <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['prescriptions']->value, 'prescription');
$_smarty_tpl->tpl_vars['prescription']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['prescription']->value) {
$_smarty_tpl->tpl_vars['prescription']->do_else = false;
?>
            <?php if ($_smarty_tpl->tpl_vars['prescription']->value->get_active() > 0) {?>
            <tr>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->drug);?>
&nbsp;</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_size());
echo text($_smarty_tpl->tpl_vars['prescription']->value->get_unit_display());?>
&nbsp;
                    <?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_dosage_display());?>
</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_quantity());?>
</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_refills());?>
</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_date_added());?>
</td>
            </tr>
            <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </tbody>
    </table>
</div>
<?php }
}
}
