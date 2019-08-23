{literal}
<script type="text/javascript">
    CRM.$(function() {
       CRM.$('#rule_action_entity').change(function() {
           // get the selected entity
           var entity = CRM.$('#rule_action_entity').val();

           // get the field list
           var fieldList = CRM.$('#rule_action_field');

           // get the fields of the selected entity
           CRM.api3(entity, 'getfields', {
               "api_action": ""
           }).then(function(result) {
               // get the current selected (or default) value
               var currentValue = CRM.$('#rule_action_field').val();

               // clear list
               CRM.$('#rule_action_field').empty();

               // add items
               for(var field in result['values']) {
                   fieldList.append(CRM.$('<option></option>').attr('value', result['values'][field].name).text(result['values'][field].title));

                   // set the default value (if it matches)
                   if (field == currentValue) {
                       fieldList.val(currentValue);
                   }
               }


           }, function(error) {
               alert("Can't get fields of entity: " + entity);
           });
       });

       CRM.$('#rule_action_field').change(function() {
           // get the selected entity
           var entity = CRM.$('#rule_action_entity').val();

           // get the selected field
           var field = CRM.$('#rule_action_field').val();

          // get the options of the selected field
           CRM.api3(entity, 'getoptions', {
               "field": field
           }).then(function(result) {
               // get the parent div of the value field
               var parentDiv = CRM.$('#rule_action_value_div');

               // remove the current value field
               parentDiv.empty();

               // check if this field has options
               if (result.is_error === 0) {
                   // add a select tag with the options
                   parentDiv.append(CRM.$('<select></select>').attr({
                       'name': 'rule_action_value',
                       'id': 'rule_action_value'
                    }));
                   CRM.$('#rule_action_value').addClass('crm-form-select');
                   var valueList = CRM.$('#rule_action_value');

                   // add the options
                   for(var field in result['values']) {
                       valueList.append(CRM.$('<option></option>').attr('value', field).text(result['values'][field]));
                       console.log(result['values'][field]);
                   }
               }
               else {
                   // add a input tag
                   parentDiv.append(CRM.$('<input type="text">').attr({
                       'name': 'rule_action_value',
                       'id': 'rule_action_value'
                   }));
                   CRM.$('#rule_action_value').addClass('crm-form-text');
               }
           }, function(error) {
               // oops
           });
       });

       //force population of field list
       CRM.$('#rule_action_entity').trigger('change');
    });

</script>
{/literal}
