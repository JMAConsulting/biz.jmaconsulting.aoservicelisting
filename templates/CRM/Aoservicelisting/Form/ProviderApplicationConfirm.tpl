{* HEADER *}
{crmScope extensionKey='biz.jmaconsulting.aoservicelisting'}

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
</fieldset>
<div class="crm-public-form-item crm-section listing">
  {include file="CRM/UF/Form/Block.tpl" fields=$profile}
  <p>{ts}The primary contact's name, email and phone will be used by Autism Ontario to communicate about the Service Listing and application{/ts}</p>
</div>

<div class="crm-public-form-item crm-section">
  {section name='i' start=1 loop=11}
  {assign var='rowNumber' value=$smarty.section.i.index}
  <div id="work_address-{$rowNumber}" class=" {cycle values="odd-row,even-row"} crm-section form-item">
  {if $rowNumber < 2}
    <fieldset><legend><span class="fieldset-legend">{ts}Primary Work Location{/ts}</span></legend>
    <p>{ts}Work location information is included in public service provider listings{/ts}</p>
  {else}
    <fieldset><legend><span class="fieldset-legend">{ts 1=$rowNumber-1}Supplementary Work Location %1{/ts}</span></legend>
  {/if}
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
    </fieldset>
  </div>
  {/section}
</div>
<div class="crm-public-form-item crm-section listing1">
  {include file="CRM/UF/Form/Block.tpl" fields=$profile1}
</div>
<div class="crm-public-form-item crm-section">
  {section name='s' start=1 loop=22}
    {assign var='rowNum' value=$smarty.section.s.index}
    <div id="staff_member-{$rowNum}" class=" {cycle values="odd-row,even-row"} crm-section form-item">
      <fieldset>
        <legend>
          <span class="fieldset-legend">{ts 1=$rowNum}Staff Person %1{/ts}</span>
        </legend>
        <div class="label">{$form.staff_first_name.$rowNum.label}</div>
        <div class="content">{$form.staff_first_name.$rowNum.html}</div>
        <div class="clear"></div><br/>
        <div class="label">{$form.staff_last_name.$rowNum.label}</div>
        <div class="content">{$form.staff_last_name.$rowNum.html}</div>
        <div class="clear"></div><br/>
        <div class="label">{$form.staff_record_regulator.$rowNum.label}</div>
        <div class="content">{$form.staff_record_regulator.$rowNum.html}</div>
        <div class="clear"></div><br/>
      </fieldset>
    </div>
  {/section}
</div>
<p>{ts}For each staff person who is a regulated professional, add a link to their listing on their College's site showing their status. If a URL directly to the record is not available, a link to the regulator's site is sufficient. For a camp, link to the camp's accreditation. Staff information is used by Autism Ontario for verification purposes and is not displayed to the public{/ts}</p>
<div class="crm-public-form-item crm-section listing2">
  {include file="CRM/UF/Form/Block.tpl" fields=$profile2}
</div>

{if !empty($campFields)}
{section name='c' start=1 loop=21}
  {assign var='rowN' value=$smarty.section.c.index}
  {if !empty($campFields.$rowN)}
    <div id="camp_session-{$rowN}" class="camp-section camp-section-{$rowN} {cycle values="odd-row,even-row"}">
      {foreach from=$campFields.$rowN item=field}
      <div class="crm-section">
        <div class="label">{$form.$field.label}</div>
        <div class="content">{$form.$field.html}</div>
        <div class="clear"></div>
      </div>
      {/foreach}
    </div>
  {/if}
{/section}
{/if}

{* FOOTER *}
<div class="crm-public-form-item crm-section waiver-section">
  <p>{ts}I certify that all of the information contained in my listing is true and I have the authority to add this listing to Autism Ontario and I understand that failure to comply with the above criteria may result in the removal of my listing{/ts}</p>
  <div class="label">{$form.waiver_field.label}</div>
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
      var serviceListing = $('[name=listing_type]').val();
      if (serviceListing == "1") {
        $('.edit-row-organization_name').hide();
        $('.edit-row-organization_email').hide();
      }
      else {
        $('.edit-row-organization_name').show();
        $('.edit-row-organization_email').show();
      }

      $('[id^=staff_first_name_]').each(function() {
        if ($(this).parent().text().length < 2) {
          $(this).parent().parent().parent().parent().addClass('hiddenElement');
        }
      });
      $('[id^=work_address_]').each(function() {
        if ($(this).parent().text().length < 2) {
          $(this).parent().parent().parent().parent().addClass('hiddenElement');
        }
      });
    });
  </script>
{/literal}
{/crmScope}
