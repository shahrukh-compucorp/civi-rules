<h3>{$ruleActionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block-set-value">
    <div class="crm-section">
        <div class="label">{$form.rule_action_entity.label}</div>
        <div class="content">{$form.rule_action_entity.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.rule_action_field.label}</div>
        <div class="content">{$form.rule_action_field.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.rule_action_value.label}</div>
        <div class="content" id="rule_action_value_div">{$form.rule_action_value.html}</div>
        <div class="clear"></div>
    </div>
</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{include file="CRM/CivirulesActions/Entity/Form/SetValueJs.tpl"}
