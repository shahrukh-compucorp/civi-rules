{* dialog for rule help text *}
<div id="civirule_helptext_dialog-block">
  <p><label id="civirule_help_text-value"></label></p>
</div>
<div class="crm-content-block crm-block">
  <div id="help">
    The existing CiviRules are listed below. You can manage, delete, disable/enable or add a rule. 
  </div>
  <div class="action-link">
    <a class="button new-option" href="{$add_url}">
      <span><div class="icon add-icon ui-icon-circle-plus"></div>{ts}Add CiviRule{/ts}</span>
    </a>
  </div>
  <div id="civirule_wrapper" class="dataTables_wrapper">
    {include file="CRM/common/jsortable.tpl"}
    <table id="civirule-table" class="display">
      <thead>
        <tr>
          <th id="sortable">{ts}Rule Label{/ts}</th>
          <th id="sortable">{ts}Trigger{/ts}</th>
          <th id="sortable">{ts}Tag(s){/ts}</th>
          <th id="nosort">{ts}Description{/ts}</th>
          <th id="sortable">{ts}Active?{/ts}</th>
          <th id="sortable">{ts}Date Created{/ts}</th>
          <th id="sortable">{ts}Created By{/ts}</th>
          <th id="nosort"></th>
        </tr>
      </thead>
      <tbody>
        {assign var="row_class" value="odd-row"}
        {foreach from=$rules key=rule_id item=rule}
          <tr id="row_{$rule.id}" class={$row_class}>
            <td hidden="1">{$rule.id}</td>
            <td>{$rule.label}</td>
            <td>{$rule.trigger_label}</td>
            <td>{$rule.tags}</td>
            <td>{$rule.description}
              {if (!empty($rule.help_text))}
                <a id="civirule_help_text_icon" class="crm-popup medium-popup helpicon" onclick="showRuleHelp({$rule.id})" href="#"></a>
              {/if}
            <td>{$rule.is_active}</td>
            </td>
            <td>{$rule.created_date|crmDate}</td>
            <td>{$rule.created_contact_name}</td>
            <td>
              <span>
                {foreach from=$rule.actions item=action_link}
                  {$action_link}
                {/foreach}
              </span>
            </td>
          </tr>
          {if $row_class eq "odd-row"}
            {assign var="row_class" value="even-row"}
          {else}
            {assign var="row_class" value="odd-row"}                        
          {/if}
        {/foreach}
      </tbody>
    </table>    
  </div>
  <div class="action-link">
    <a class="button new-option" href="{$add_url}">
      <span><div class="icon add-icon ui-icon-circle-plus"></div>{ts}Add CiviRule{/ts}</span>
    </a>
  </div>
</div>

{literal}
  <script>
    function showRuleHelp(ruleId) {
      console.log('rule id is ' + ruleId);
      CRM.api3('CiviRuleRule', 'getsingle', {"id": ruleId})
          .done(function(result) {
            console.log(result);
            cj("#civirule_helptext_dialog-block").dialog({
              width: 600,
              height: 300,
              title: "Help for Rule " + result.label,
              buttons: {
                "Done": function() {
                  cj(this).dialog("close");
                }
              }
            });
            cj("#civirule_helptext_dialog-block").html(result.help_text);
          });
    };
  </script>
{/literal}


