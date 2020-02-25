{* HEADER *}
{crmScope extensionKey='biz.jmaconsulting.aoonestopshop'}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

  <div class="crm-section edit-row-{$form.provider_type.id}">
    <div class="label">{$form.provider_type.label}</div>
    <div class="content">{$form.provider_type.html}</div>
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

  <div class="crm-public-form-item crm-section">
    {section name='i' start=1 loop=10}
    {assign var='rowNumber' value=$smarty.section.i.index}
    <div id="work_address-{$rowNumber}" class="{if $rowNumber > 1}hiddenElement{/if} {cycle values="odd-row,even-row"} crm-section form-item">
      <p>{ts}Work location information is include in public service provider listings{/ts}</p>
      <div class="label">{$form.phone.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span> </div>
      <div class="content">{$form.phone.$rowNumber.html}</div>
      <div class="clear"></div><br/>
      <div class="label">{$form.work_address.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
      <div class="content">{$form.work_address.$rowNumber.html}</div>
      <div class="clear"></div><br/>
      <div class="label">{$form.city.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
      <div class="content">{$form.city.$rowNumber.html}</div>
      <div class="clear"></div><br/>
      <div class="label">{$form.postal_code.$rowNumber.label}  <span class="crm-marker" title="This field is required.">*</span></div>
      <div class="content">{$form.postal_code.$rowNumber.html}</div>
      <div class="clear"></div><br/>
      {if $rowNumber neq 1}
         <div><a href=# class="remove_item_employee crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
      {/if}
    </div>
    {/section}
  </div>
  <span id="add-another-employee" class="crm-hover-button"><a href=#>{ts}Add another employer{/ts}</a></span>
  <div class="crm-public-form-item crm-section">
    {section name='s' start=1 loop=10}
    {assign var='rowNum' value=$smarty.section.s.index}
    <div id="staff_member-{$rowNumber}" class="{if $rowNum > 1}hiddenElement{/if} {cycle values="odd-row,even-row"} crm-section form-item">
      <div class="label">{$form.staff_first_name.$rowNum.label}</div>
      <div class="content">{$form.staff_first_name.$rowNum.html}</div>
      <div class="clear"></div><br/>
      <div class="label">{$form.staff_last_name.$rowNum.label}</div>
      <div class="content">{$form.staff_last_name.$rowNum.html}</div>
      <div class="clear"></div><br/>
      <div class="label">{$form.staff_record_regulator.$rowNum.label}</div>
      <div class="content">{$form.staff_record_regulator.$rowNum.html}</div>
      <div class="clear"></div><br/>
      {if $rowNum neq 1}
         <div><a href=# class="remove_item_staff crm-hover-button" style="float:right;"><b>{ts}Hide{/ts}</b></a></div>
      {/if}
    </div>
    {/section}
  </div>
  <p>{ts}For each staff person who is a regulated professional, add a link to their listing on their College's site showing their status. If a url directly to the record is not available a link to the regulator's site is sufficient. For a camp, link to the camp's accreditation. Staff information is used by Autism Ontario for verification purposes and is not displayed to the public{/ts}</p>
  <span id="add-another-staff" class="crm-hover-button"><a href=#>{ts}Add another staff person who is a regulated professional{/ts}</a></span>


{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-profile legend').hide();
  var serviceProvider = $('[name=provider_type]:checked').val();
  if (serviceProvider == "1") {
    $('.edit-row-organization_name').hide();
    $('.edit-row-organization_email').hide();
  }
  else {
    $('.edit-row-organization_name').show();
    $('.edit-row-organization_email').show();
  }
  $('[name=provider_type]').on('change', function() {
    if ($(this).val() == "1") {
      $('.edit-row-organization_name').hide();
      $('.edit-row-organization_email').hide();
    }
    else {
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
  });
});
</script>
{/literal}
{/crmScope}
