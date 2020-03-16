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
<div class="crm-section edit-row-{$form.website.id}">
  <div class="label">{$form.website.label} <span class="crm-marker" title="This field is required.">*</span></div>
  <div class="content">{$form.website.html}</div>
  <div class="clear"></div>
</div>
<fieldset>
  <legend><span class="fieldset-legend">{ts}Primary Contact{/ts}</span></legend>
  <div class="crm-section edit-row-{$form.primary_first_name.id}">
    <div class="label">{$form.primary_first_name.label}  <span class="crm-marker" title="This field is required.">*</span></div>
    <div class="content">{$form.primary_first_name.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.primary_last_name.id}">
    <div class="label">{$form.primary_last_name.label}  <span class="crm-marker" title="This field is required.">*</span></div>
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

<div class="crm-public-form-item crm-section">
  {section name='s' start=1 loop=21}
    {assign var='rowNum' value=$smarty.section.s.index}
    <div id="staff_member-{$rowNum}" class="{if $rowNum > 1}hiddenElement{/if} {cycle values="odd-row,even-row"} crm-section form-item">
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
      $('.crm-profile legend').hide();
      $('#crm-container.crm-public .label').css('font-size', '16px');
      $('.crm-clear-link').hide();
      var serviceProvider = $('[name=listing_type]:checked').val();
      if (serviceProvider == "1") {
        $('.edit-row-organization_name').hide();
        $('.edit-row-organization_email').hide();
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'none');
      }
      else {
        $('.edit-row-organization_name').show();
        $('.edit-row-organization_email').show();
        $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
        $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'all');
      }
      $('[name=listing_type]').on('change', function() {
        if ($(this).val() == "1") {
          $('.edit-row-organization_name').hide();
          $('.edit-row-organization_email').hide();
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'none');
        }
        else {
          $('.edit-row-organization_name').show();
          $('.edit-row-organization_email').show();
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"][value="1"]').prop({'checked': true});
          $('*[data-crm-custom="service_provider_details:Display_First_Name_and_Last_Name_in_public_listing"]').parent('div.content').css('pointer-events', 'all');
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
      if ($('#custom_863_3').prop('checked')) {
        $('#camp_session-1, #camp-section').removeClass('hiddenElement');
        $('#add-another-camp').show();
        $('[id^=custom_859_').each(function() {
          if ($(this).val().length) {
            $(this).parent().parent().parent().parent().removeClass('hiddenElement');
          }
        });
      }
      $('#custom_863_3').on('change', function() {
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
      });
      $('#primary_last_name').change(function() {
        $('#staff_last_name_1').val($(this).val()).trigger('change');
      });
      $('[name=custom_862]').change(function() {
        if ($(this).val() == "1") {
          $('.editrow_custom_863-section').show();
          $('[id^=staff_record_regulator]').each(function() {
            if (!$(this).parent().parent().parent().parent().hasClass('hiddenElement')) {
              $(this).parent().parent().show();
            }
          });
          $('#regulated-staff-message').show();
        }
        else {
          $('.editrow_custom_863-section').hide();
          $('[id^=custom_863_]').each(function() {
             if ($(this).prop('checked')) {
               $(this).prop('checked', false).trigger('change');
             }
          });
          $('[id^=staff_record_regulator]').each(function() {
            if (!$(this).parent().parent().parent().parent().hasClass('hiddenElement')) {
              $(this).val('').trigger('change');
              $(this).parent().parent().hide();
            }
          });
          $('#regulated-staff-message').hide();
        }
      });
      var checkboxCustomFIelds = ['863', '865', '866'];
      $.each(checkboxCustomFIelds, function(index, cfield) {
        $('[id^=custom_' + cfield + ']').each(function() {
          $(this).add($(this).prev()).add($(this).next()).wrapAll('<span class="custom-checkbox">');
        });
      });
    });
  </script>
{/literal}
{/crmScope}
