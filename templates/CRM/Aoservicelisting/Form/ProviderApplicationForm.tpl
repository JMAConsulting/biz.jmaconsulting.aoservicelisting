{* HEADER *}
{crmScope extensionKey='biz.jmaconsulting.aoservicelisting'}
<div class="crm-section edit-row-{$form.listing_type.id}">
  <div class="label">{$form.listing_type.label} <span class="crm-marker" title="This field is required.">*</span></div>
  <div class="content">{$form.listing_type.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section edit-row-{$form.organization_name.id}">
  <div class="label">{$form.organization_name.label} <span class="crm-marker" title="This field is required.">*</span></div>
  <div class="content">{$form.organization_name.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section edit-row-{$form.organization_email.id}">
  <div class="label">{$form.organization_email.label} <span class="crm-marker" title="This field is required.">*</span></div>
  <div class="content">{$form.organization_email.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section edit-row-{$form .website.id}">
  <div class="label">{$form.website.label}</div>
  <div class="content">{$form.website.html}</div>
  <div class="clear"></div>
</div>
<fieldset>
  <legend><span class="fieldset-legend">{ts}Primary Contact{/ts}</span></legend>
  <div class="crm-section edit-row-{$form.primary_first_name.id}">
    <div class="label">{$form.primary_first_name.label} <span class="crm-marker" title="This field is required.">*</span></div>
    <div class="content">{$form.primary_first_name.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.primary_last_name.id}">
    <div class="label">{$form.primary_last_name.label} <span class="crm-marker" title="This field is required.">*</span></div>
    <div class="content">{$form.primary_last_name.html}</div>
    <div class="clear"></div>
  </div>
  </fieldset>
  <div class="crm-public-form-item crm-section listing">
    {include file="CRM/UF/Form/Block.tpl" fields=$profile}
    <p>{ts}The primary contact's name, email and phone will be used by Autism Ontario to communicate about the Service Listing and application{/ts}</p>
  </div>

<div class="crm-public-form-item crm-section">
  {section name='i' start=1 loop=11}
  {assign var='rowNumber' value=$smarty.section.i.index}
  <div id="work_address-{$rowNumber}" class="{if $rowNumber > 1}hiddenElement{/if} {cycle values="odd-row,even-row"} crm-section form-item">
    {if $rowNumber < 2}
      <fieldset><legend><span class="fieldset-legend">{ts}Primary Work Location{/ts}</span></legend>
      <p>{ts}Work location information is included in public service provider listings{/ts}</p>
    {else}
      <fieldset><legend><span class="fieldset-legend">{ts 1=$rowNumber-1}Supplementary Work Location %1{/ts}</span></legend>
    {/if}
      <div class="crm-section">
        <div class="label">{$form.phone.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span> </div>
        <div class="content">{$form.phone.$rowNumber.html}</div>
        <div class="clear"></div>
      </div>
      <div class="crm-section">
        <div class="label">{$form.work_address.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
        <div class="content">{$form.work_address.$rowNumber.html}</div>
        <div class="clear"></div>
      </div>
      <div class="crm-section">
        <div class="label">{$form.city.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
        <div class="content">{$form.city.$rowNumber.html}</div>
        <div class="clear"></div>
      </div>
      <div class="crm-section">
        <div class="label">{$form.postal_code.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
        <div class="content">{$form.postal_code.$rowNumber.html}</div>
        <div class="clear"></div>
      </div>
      {if $rowNumber neq 1}
         <div><a href=# class="remove_item_employee crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
      {/if}
    </fieldset>
  </div>
  {/section}
</div>
<span id="add-another-employee" class="crm-hover-button"><a href=#>{ts}Add another work location{/ts}</a></span>
<div class="crm-public-form-item crm-section listing1">
  {include file="CRM/UF/Form/Block.tpl" fields=$profile1}
</div>

<div class="aba_staff_members crm-section">
  {section name='a' start=1 loop=21}
    {assign var='rowa' value=$smarty.section.a.index}
    <div id="aba_staff_member-{$rowa}" class="hiddenElement {cycle values="odd-row,even-row"} crm-section form-item">
      <fieldset>
        <legend class="aba-legend">
          <span class="fieldset-legend">{ts 1=$rowa}Staff Person %1{/ts}</span>
        </legend>
        <div class="crm-section">
          <div class="label">{$form.aba_first_name.$rowa.label}</div>
          <div class="content">{$form.aba_first_name.$rowa.html}</div>
          <div class="clear"></div>
        </div>
        <div class="crm-section">
          <div class="label">{$form.aba_last_name.$rowa.label}</div>
          <div class="content">{$form.aba_last_name.$rowa.html}</div>
          <div class="clear"></div>
        </div>
        <div class="crm-section">
          <div class="label">{$form.$CERTIFICATE_NUMBER.$rowa.label}</div>
          <div class="content">{$form.$CERTIFICATE_NUMBER.$rowa.html}</div>
          <div class="clear"></div>
        </div>
        {if $rowa neq 1}
          <div><a href=# class="remove_item_aba crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
        {/if}
      </fieldset>
    </div>
  {/section}
</div>
<span id="add-another-aba" class="crm-hover-button"><a href=#>{ts}Add another ABA certified staff person{/ts}</a></span>
<div class="crm-public-form-item crm-section listing3">
  {include file="CRM/UF/Form/Block.tpl" fields=$profile3}
</div>

<div class="staff_members crm-public-form-item crm-section">
  {section name='s' start=1 loop=21}
    {assign var='rowNum' value=$smarty.section.s.index}
    <div id="staff_member-{$rowNum}" class="hiddenElement {cycle values="odd-row,even-row"} crm-section form-item">
      <fieldset>
        <legend>
          <span class="fieldset-legend">{ts 1=$rowNum}Staff Person %1{/ts}</span>
        </legend>
        <div class="crm-section">
          <div class="label">{$form.staff_first_name.$rowNum.label}</div>
          <div class="content">{$form.staff_first_name.$rowNum.html}</div>
          <div class="clear"></div>
        </div>
        <div class="crm-section">
          <div class="label">{$form.staff_last_name.$rowNum.label}</div>
          <div class="content">{$form.staff_last_name.$rowNum.html}</div>
          <div class="clear"></div>
        </div>
        <div class="crm-section">
          <div class="label">{$form.staff_record_regulator.$rowNum.label}</div>
          <div class="content">{$form.staff_record_regulator.$rowNum.html}</div>
          <div class="clear"></div>
        </div>
        {if $rowNum neq 1}
          <div><a href=# class="remove_item_staff crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
        {/if}
      </fieldset>
    </div>
  {/section}
</div>
<div id="regulated-staff-message"><p>{ts}For each staff person who is a regulated professional, add a link to their listing on their College's site showing their status. If a URL directly to the record is not available, a link to the regulator's site is sufficient. For a camp, link to the camp's accreditation. Staff information is used by Autism Ontario for verification purposes and is not displayed to the public{/ts}</p>
<span id="add-another-staff" class="crm-hover-button"><a href=#>{ts}Add another staff person who is a regulated professional{/ts}</a></span></div>
<div class="crm-public-form-item crm-section listing2">
  {include file="CRM/UF/Form/Block.tpl" fields=$profile2}
</div>

{section name='c' start=1 loop=21}
  {assign var='rowN' value=$smarty.section.c.index}
  <div id="camp_session-{$rowN}" class="camp-section camp-section-{$rowN} {if $rowN > 1}hiddenElement{/if} {cycle values="odd-row,even-row"}">
    {foreach from=$campFields.$rowN item=field}
    <div class="crm-section">
      <div class="label">{$form.$field.label}</div>
      <div class="content">{$form.$field.html}</div>
      <div class="clear"></div>
    </div>
    {/foreach}
    {if $rowN neq 1}
       <div><a href=# class="remove_item_camp crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
    {/if}
  </div>
{/section}
<span id="add-another-camp" class="crm-hover-button"><a href=#>{ts}Add another session{/ts}</a></span>

{* FOOTER *}
<div class="crm-public-form-item crm-section waiver-section">
  <p>{ts}I certify that all of the information contained in my listing is true and I have the authority to add this listing to Autism Ontario. I understand that failure to comply with the above criteria may result in the removal of my listing{/ts}</p>
  <div class="label">{$form.waiver_field.label} <span class="crm-marker" title="This field is required.">*</span></div>
  <div class="content">{$form.waiver_field.html}</div>
  <p>{ts}Please note Autism Ontario reserves the right to refuse, suspend, or remove an applicant or previously approved member of Autism Ontario Listing Service{/ts}</p>
</div>
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
  <script type="text/javascript">
    CRM.$(function($) {
      // Add class onto the label of listing_type radio_elements
      $('[for$=listing_type]').each(function() {
        $(this).addClass('listing_type_label_' + $(this).prev().val());
        if (CRM.config.locale != "en_US") {
          $(this).addClass('language_french');
        }
      });
      var abaServices = $('[name=' + {/literal}'{$ABA_SERVICES}'{literal} + ']:checked').val();
      var abacredentialsSectionID = '.editrow_' + {/literal}'{$ABA_CREDENTIALS}'{literal} + '-section';
      var abastaffSectionID = '.aba_staff_members';
      if (abaServices == "1") {
        $(abacredentialsSectionID).show();
        $(abastaffSectionID).show();
        if ($('[name=listing_type]:checked').val() == 2) {
          $('#add-another-aba').show();
        }
      }
      else {
        $(abacredentialsSectionID).hide();
        $(abastaffSectionID).hide();
        if ($('[name=listing_type]:checked').val() == 2) {
          $('#add-another-aba').hide();
        }
      }
      $('[name=' + {/literal}'{$ABA_SERVICES}'{literal} + ']').change(function() {
        if ($(this).val() == "1") {
          $('#aba_staff_member-1').removeClass('hiddenElement');
          $(abacredentialsSectionID).show();
          $(abastaffSectionID).show();
          if ($('[name=listing_type]:checked').val() == 2) {
            $('#add-another-aba').show();
          }
        }
        else {
          $('#' + {/literal}'{$CERTIFICATE_NUMBER}'{literal} + '_1').val('').trigger('change');
          $('#editrow-' + {/literal}'{$ABA_CREDENTIALS}'{literal} + ' input[type=checkbox]:checked').each(function(e) {
            $(this).attr('checked', false);
          });
          $('#aba_staff_member-1').addClass('hiddenElement');
          $(abacredentialsSectionID).hide();
          $(abastaffSectionID).hide();
          if ($('[name=listing_type]:checked').val() == 2) {
            $('#add-another-aba').hide();
          }
        }
      });

      var servicecheckedcount=0;
      var serviceunchekecount=0;
      var abservices = $('#editrow-' + {/literal}'{$ABA_CREDENTIALS}'{literal} + ' input[type=checkbox]');
      abservices.each(function() {
        if ($(this).prop('checked') && $(this).attr('id').indexOf('None') === -1) {
          servicecheckedcount++;
        }
        if (!$(this).prop('checked') && $(this).attr('id').indexOf('None') === -1) {
          serviceunchekecount++;
        }
      });
      showABA(servicecheckedcount, serviceunchekecount);
      abservices.change(function() {
          var checked = 0;
          var unchecked = 5;
          abservices.each(function() {
            if ($(this).prop('checked') && $(this).attr('id').indexOf('None') === -1) {
              checked++;
            }
            if (!$(this).prop('checked') && $(this).attr('id').indexOf('None') === -1) {
              unchecked--;
            }
          });
          showABA(checked, abservices.filter(':checked'));
          hideABA(unchecked, checked);
      });

      function showABA(countcheck, service) {
        if (countcheck) {
          $('#aba_staff_member-1').removeClass('hiddenElement');
          for (var i=1; i<=countcheck; i++) {
              if ($('[name=listing_type]:checked').val() == "2") {
                  $('#aba_staff_member-' + i).removeClass('hiddenElement');
              }
          }
        }
      }

      function hideABA(unchecked, checked) {
        for (i=checked+1; i<=unchecked; i++) {
          if (!$('#aba_staff_member-' + i).hasClass('hiddenElement') && i > 1) {
            $('#aba_staff_member-' + i).addClass('hiddenElement');
            $('#' + {/literal}'{$CERTIFICATE_NUMBER}'{literal} + '_' + i).val('').trigger('change');
            $('#aba_last_name_' + i).val('').trigger('change');
            $('#aba_first_name_' + i).val('').trigger('change');
          }
        }
      }
      $('.crm-profile legend:not(.aba-legend)').hide();
      $('#crm-container.crm-public .label').css('font-size', '16px');
      $('.crm-clear-link').hide();

      var serviceProvider = $('[name=listing_type]:checked').val();
      if (serviceProvider == "1") {
        $('.edit-row-organization_name').hide();
        $('.edit-row-organization_email').hide();
        $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
        $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'none');
        $('#add-another-staff, #add-another-aba').hide();
        $('#aba_first_name_1').parent().parent().hide();
        $('#aba_last_name_1').parent().parent().hide();
        $('#staff_first_name_1').parent().parent().hide();
        $('#staff_last_name_1').parent().parent().hide();
      }
      else {
        $('.edit-row-organization_name').show();
        $('.edit-row-organization_email').show();
        $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
        $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'all');
        if (abaServices == "1") {
            $('#add-another-staff, #add-another-aba').show();
        }
        $('#aba_first_name_1').parent().parent().show();
        $('#aba_last_name_1').parent().parent().show();
        $('#staff_first_name_1').parent().parent().show();
        $('#staff_last_name_1').parent().parent().show();
      }
      $('[name=listing_type]').on('change', function() {
        var servicecheckedcount = 0;
        var serviceunchekecount = 5;
        $('#editrow-' + {/literal}'{$ABA_CREDENTIALS}'{literal} + ' input[type=checkbox]').each(function() {
          if ($(this).prop('checked') && $(this).attr('id').indexOf('None') === -1) {
            servicecheckedcount++;
          }
          if (!$(this).prop('checked') && $(this).attr('id').indexOf('None') === -1) {
            serviceunchekecount--;
          }
        });
        var service = $('#editrow-' + {/literal}'{$REGULATED_SERVICE_CF}'{literal} + ' input[type=checkbox]');
        if ($(this).val() == "1") {
          $('.edit-row-organization_name').hide();
          $('.edit-row-organization_email').hide();
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'none');
          $('#add-another-staff, #add-another-aba').hide();
          $('#aba_first_name_1').parent().parent().hide();
          $('#aba_last_name_1').parent().parent().hide();
          $('#staff_first_name_1').parent().parent().hide();
          $('#staff_last_name_1').parent().parent().hide();
          hideStaff(parseInt(service.filter(':checked').length), 1);
          hideABA(serviceunchekecount, 0);
        }
        else {
          $('.edit-row-organization_name').show();
          $('.edit-row-organization_email').show();
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'all');
          if ($('[name=' + {/literal}'{$ABA_SERVICES}'{literal} + ']:checked').val() == "1") {
              $('#add-another-staff, #add-another-aba').show();
          }
          $('#aba_first_name_1').parent().parent().show();
          $('#aba_last_name_1').parent().parent().show();
          $('#staff_first_name_1').parent().parent().show();
          $('#staff_last_name_1').parent().parent().show();
          showStaff(parseInt(services.filter(':checked').length), services.filter(':checked'));
          showABA(servicecheckedcount, serviceunchekecount);
        }
      });

      $('[id^="staff_member-"]').each(function() {
        var section = $(this);
        $(this).find('.content > input').each(function() {
          if ($(this).val().length) {
            section.removeClass('hiddenElement');
          }
        });
      });

      $('[id^="work_address-"]').each(function() {
        var workSection = $(this);
        $(this).find('.content > input').each(function() {
          if ($(this).val().length) {
            workSection.removeClass('hiddenElement');
          }
        });
      });

      $('#add-another-employee').on('click', function(e) {
        e.preventDefault();
        if ($('[id^="work_address-"]').hasClass("hiddenElement")) {
          $('[id^="work_address-"].hiddenElement:first').removeClass('hiddenElement');
        }
      });
      $('.remove_item_employee').on('click', function(e) {
        e.preventDefault();
        var row = $(this).closest('[id^="work_address-"]');
        row.addClass('hiddenElement');
        row.find('div.content').each(function() {
          $(this).find('input').val('').trigger('change');
        });
      });
      // Hide/show staff
      $('#add-another-staff').on('click', function(e) {
        e.preventDefault();
        if ($('[id^="staff_member-"]').hasClass("hiddenElement")) {
          $('[id^="staff_member-"].hiddenElement:first').removeClass('hiddenElement');
        }
      });
      $('.remove_item_staff').on('click', function(e) {
        e.preventDefault();
        var row = $(this).closest('[id^="staff_member-"]');
        row.addClass('hiddenElement');
        row.find('div.content').each(function() {
          $(this).find('input').val('').trigger('change');
        });
      });

      // Hide/show ABA staff
      $('#add-another-aba').on('click', function(e) {
          e.preventDefault();
          if ($('[id^="aba_staff_member-"]').hasClass("hiddenElement")) {
              $('[id^="aba_staff_member-"].hiddenElement:first').removeClass('hiddenElement');
          }
      });
      $('.remove_item_aba').on('click', function(e) {
          e.preventDefault();
          var row = $(this).closest('[id^="aba_staff_member-"]');
          row.addClass('hiddenElement');
          row.find('div.content').each(function() {
              $(this).find('input').val('').trigger('change');
          });
      });

      $('#add-another-camp').on('click', function(e) {
        e.preventDefault();
        if ($('[id^="camp_session-"]').hasClass("hiddenElement")) {
          $('[id^="camp_session-"].hiddenElement:first').removeClass('hiddenElement');
        }
      });
      $('.remove_item_camp').on('click', function(e) {
        e.preventDefault();
        var row = $(this).closest('[id^="camp_session-"]');
        row.addClass('hiddenElement');
        row.find('[id^=custom_]').val('').trigger('change');
      });
      $('#camp_session-1, #camp-section').addClass('hiddenElement');

      $('#add-another-camp').hide();
      var rsSelector = '#' + {/literal}'{$REGULATED_SERVICE_CF}'{literal} + '_3';
      if ($(rsSelector).prop('checked')) {
        $('#camp_session-1, #camp-section').removeClass('hiddenElement');
        $('#add-another-camp').show();
      }
      $(rsSelector).on('change', function() {
       if ($(this).prop('checked')) {
         $('#camp_session-1, #camp-section').removeClass('hiddenElement');
         $('#add-another-camp').show();
       }
       else {
         $('.camp-section').each(function() {
           $(this).addClass('hiddenElement');
           $(this).find('[id^=custom_]').val('').trigger('change');
         });
         $('#add-another-camp').hide();
       }
      });
      $('[id^=staff_record_regulator_]').each(function() {
        if ($(this).val().length) {
          $(this).parent().parent().parent().removeClass('hiddenElement');
        }
      });

      // Add domains as default values
      {/literal}{if $isCreate}{literal}
      var services = $('#editrow-' + {/literal}'{$REGULATED_SERVICE_CF}'{literal} + ' input[type=checkbox]');
      showStaff(services.filter(':checked').length, services.filter(':checked'));
      services.change(function() {
        var checked = parseInt(services.filter(':checked').length);
        var unchecked = parseInt(services.filter(':not(:checked)').length);
        showStaff(checked, services.filter(':checked'));
        hideStaff(unchecked, checked);
      });

      function showStaff(countcheck, service) {
        if (countcheck) {
          for (var i=1; i<=countcheck; i++) {
            if ($('[name=listing_type]:checked').val() == "2") {
              $('#staff_member-' + i).removeClass('hiddenElement');
            }
          }
        }
      }

      function hideStaff(unchecked, checked) {
        for (i=checked+1; i<=unchecked; i++) {
          if (!$('#staff_member-' + i).hasClass('hiddenElement') && i > 1) {
            $('#staff_member-' + i).addClass('hiddenElement');
            $('#staff_last_name_' + i).val('').trigger('change');
            $('#staff_first_name_' + i).val('').trigger('change');
            $('#staff_record_regulator_' + i).val('');
          }
        }
      }

      {/literal}{/if}{literal}
      // End domain default values
      var addressFields = ['work_address_', 'phone_', 'postal_code_', 'city_'];
      $.each(addressFields, function(index, field) {
        $('[id^=' + field + ']').each(function() {
          if ($(this).val().length) {
            $(this).parent().parent().parent().removeClass('hiddenElement');
          }
        });
      });
      $('#primary_first_name').change(function() {
        $('#staff_first_name_1').val($(this).val()).trigger('change');
        $('#aba_first_name_1').val($(this).val()).trigger('change');
      });
      $('#primary_last_name').change(function() {
        $('#staff_last_name_1').val($(this).val()).trigger('change');
        $('#aba_last_name_1').val($(this).val()).trigger('change');
      });
      var selector = {/literal}'{$IS_REGULATED_SERVICE}'{literal};
      var selectorVal = $('[name=' + selector + ']:checked').val();
      var regulatedServices =  $('#editrow-' + {/literal}'{$REGULATED_SERVICE_CF}'{literal});
      if (selectorVal == "1") {
        regulatedServices.show();
        $('#regulated-staff-message, .staff_members').show();
      }
      else {
        regulatedServices.hide();
        $('#regulated-staff-message, .staff_members').hide();
      }
      $('[name=' + selector + ']').change(function() {
        var rsSelector = {/literal}'{$REGULATED_SERVICE_CF}'{literal};
        if ($(this).val() == "1") {
          $('#staff_member-1').removeClass('hiddenElement');
          $('.editrow_' + rsSelector + '-section').show();
          $('[id^=staff_record_regulator]').each(function() {
            if (!$(this).parent().parent().parent().parent().hasClass('hiddenElement')) {
              $(this).parent().parent().show();
            }
          });
          $('#staff_first_name_1').val($('#primary_first_name').val()).trigger('change');
          $('#staff_last_name_1').val($('#primary_last_name').val()).trigger('change');
          $('#regulated-staff-message, .staff_members').show();
        }
        else {
          $('#staff_member-1').addClass('hiddenElement');
          $('.editrow_' + rsSelector + '-section').hide();
          $('[id^=' + rsSelector + '_]').each(function() {
             if ($(this).prop('checked')) {
               $(this).prop('checked', false).trigger('change');
             }
          });
          $('[id^=staff_record_regulator]').each(function() {
            $(this).val('').trigger('change');
            $(this).parent().parent().hide();
          });
          $('#regulated-staff-message, .staff_members').hide();
          $('[id^=staff_first_name]').each(function() {
            if ($(this).attr('id') != 'staff_first_name_1') {
              $(this).val('').trigger('change');
            }
          });
          $('[id^=staff_last_name]').each(function() {
            if ($(this).attr('id') != 'staff_last_name_1') {
              $(this).val('').trigger('change');
            }
          });
        }
      });
      var otherLanguageField = $('#editrow-' + {/literal}'{$OTHER_LANGUAGE}'{literal});
      var languageField = $('#' + {/literal}'{$LANGUAGES}'{literal});
      var languageValues = languageField.val();
      if ($.inArray('Other Language', languageValues) !== -1) {
        otherLanguageField.show();
      }
      else {
        otherLanguageField.hide();
      }
      languageField.change(function() {
        if ($.inArray('Other Language', $(this).val()) !== -1) {
          otherLanguageField.show();
        }
        else {
          otherLanguageField.hide();
        }
      });
    });
  </script>
{/literal}
{/crmScope}
