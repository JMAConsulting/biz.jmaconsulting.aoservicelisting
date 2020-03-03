{* HEADER *}
{crmScope extensionKey='biz.jmaconsulting.aoonestop'}

<div class="crm-section edit-row-{$form.listing_type.id}">
  <div class="label">{$form.listing_type.label}</div>
  <div class="content">{$form.listing_type.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section edit-row-{$form.organization_name.id}">
  <div class="label">{$form.organization_name.label}</div>
  <div class="content">{$form.organization_name.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section edit-row-{$form.organization_email.id}">
  <div class="label">{$form.organization_email.label}</div>
  <div class="content">{$form.organization_email.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section edit-row-{$form.website.id}">
  <div class="label">{$form.website.label}</div>
  <div class="content">{$form.website.html}</div>
  <div class="clear"></div>
</div>
<fieldset>
  <legend><span class="fieldset-legend">Primary Contact</span></legend>
  <div class="crm-section edit-row-{$form.primary_first_name.id}">
    <div class="label">{$form.primary_first_name.label}</div>
    <div class="content">{$form.primary_first_name.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.primary_last_name.id}">
    <div class="label">{$form.primary_last_name.label}</div>
    <div class="content">{$form.primary_last_name.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.display_name_public.id}">
    <div class="label">{$form.display_name_public.label}</div>
    <div class="content">{$form.display_name_public.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.primary_email.id}">
    <div class="label">{$form.primary_email.label}</div>
    <div class="content">{$form.primary_email.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.display_email.id}">
    <div class="label">{$form.display_email.label}</div>
    <div class="content">{$form.display_email.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.primary_phone_number.id}">
    <div class="label">{$form.primary_phone_number.label}</div>
    <div class="content">{$form.primary_phone_number.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section edit-row-{$form.display_phone.id}">
    <div class="label">{$form.display_phone.label}</div>
    <div class="content">{$form.display_phone.html}</div>
    <div class="clear"></div>
  </div>
  <p>{ts}The primary contact's name, email and phone will be used by Autism Ontario to communicate about the is application/listing{/ts}</p>
</fieldset>

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
{foreach from=$beforeStaffCustomFields item=field}
  {assign var=fieldName value="custom_$field"}
  <div class="crm-section edit-row-custom_{$field}">
    <div class="label">{$form.$fieldName.label}</div>
    <div class="content">{$form.$fieldName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}
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
<p>{ts}For each staff person who is a regulated professional, add a link to their listing on their College's site showing their status. If a url directly to the record is not available a link to the regulator's site is sufficient. For a camp, link to the camp's accreditation. Staff information is used by Autism Ontario for verification purposes and is not displayed to the public{/ts}</p>
<span id="add-another-staff" class="crm-hover-button"><a href=#>{ts}Add another staff person who is a regulated professional{/ts}</a></span>
{foreach from=$afterStaffCustomFields item=field}
  {assign var=fieldName value="custom_$field"}
  <div class="crm-section edit-row-custom_{$field}">
    <div class="label">{$form.$fieldName.label}</div>
    <div class="content">{$form.$fieldName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}
{section name='c' start=1 loop=21}
  {assign var='rowN' value=$smarty.section.c.index}
  <div id="camp_session-{$rowN}" class="camp-section camp-section-{$rowN} {if $rowN > 1}hiddenElement{/if} {cycle values="odd-row,even-row"}">
    <div class="crm-section">
      <div class="label">{$form.custom_858.$rowN.label}</div>
      <div class="content">{$form.custom_858.$rowN.html}</div>
      <div class="clear"></div><br/>
    </div>
    <div class="crm-section">
      <div class="label" style="font-weight:inherit;">{ts}Camp Session Dates{/ts}</div>
      <div class="content">
        <div style="float:left;">{$form.custom_859.$rowN.label}<br>{$form.custom_859.$rowN.html}</div>
        <div>{$form.custom_860.$rowN.label}<br>{$form.custom_860.$rowN.html}</div>
      </div>
      <div class="clear"></div><br/>
    </div>
    {if $rowN neq 1}
       <div><a href=# class="remove_item_camp crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
    {/if}
  </div>
{/section}
<span id="add-another-camp" class="crm-hover-button"><a href=#>{ts}Add another session{/ts}</a></span>

{* FOOTER *}
<div class="crm-public-form-item crm-section waiver-section">
  <p>{ts}I certify that all of the information contained in my listing is true and I have the authority to add this listing to Autism Ontario One Stop and I understand that failure to comply with the above criteria, may result in the removal of my listing{/ts}</p>
  <div class="label">{$form.waiver_field.label}</div>
  <div class="content">{$form.waiver_field.html}</div>
  <p>{ts}Please note Autism Ontario reserves the right to refuse, suspend, or remove an applicant or previously approved member of Autism OneStop Listing Service{/ts}</p>
</div>
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
  <script type="text/javascript">
    CRM.$(function($) {
      $('.crm-profile legend').hide();
$('#crm-container.crm-public .label').css('font-size', '16px');
      var serviceProvider = $('[name=listing_type]:checked').val();
      if (serviceProvider == "1") {
        $('.edit-row-organization_name').hide();
        $('.edit-row-organization_email').hide();
        $('#display_name_public').prop({'checked': true, 'readonly': true});
      }
      else {
        $('.edit-row-organization_name').show();
        $('.edit-row-organization_email').show();
        $('#display_name_public').removeAttr('readonly');
      }
      $('[name=listing_type]').on('change', function() {
        if ($(this).val() == "1") {
          $('.edit-row-organization_name').hide();
          $('.edit-row-organization_email').hide();
          $('#display_name_public').prop({'checked': true, 'readonly': true});
        }
        else {
          $('#display_name_public').removeAttr('readonly');
          $('.edit-row-organization_name').show();
          $('.edit-row-organization_email').show();
        }
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
      $('#camp_session-1').addClass('hiddenElement');
      $('#add-another-camp').hide();
      if ($('#custom_863_3').prop('checked')) {
        $('#camp_session-1').removeClass('hiddenElement');
        $('#add-another-camp').show();
        $('[id^=custom_859_').each(function() {
          if ($(this).val().length) {
            $(this).parent().parent().parent().parent().removeClass('hiddenElement');
          }
        });
      }
      $('#custom_863_3').on('change', function() {
       if ($(this).prop('checked')) {
         $('#camp_session-1').removeClass('hiddenElement');
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
          $('.edit-row-custom_863').show();
        }
        else {
          $('.edit-row-custom_863').hide();
          $('[id^=custom_863_]').each(function() {
             if ($(this).prop('checked')) {
               $(this).prop('checked', false).trigger('change');
             }
          });
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
