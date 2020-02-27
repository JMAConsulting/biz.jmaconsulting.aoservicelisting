{* HEADER *}
{crmScope extensionKey='biz.jmaconsulting.aoonestopshop'}

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
  {if $rowNumber < 2}
    <fieldset><legend><span class="fieldset-legend">Primary Work Location</span></legend>
    <p>{ts}Work location information is include in public service provider listings{/ts}</p>
  {/if}
  <div id="work_address-{$rowNumber}" class=" {cycle values="odd-row,even-row"} crm-section form-item">
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
  </div>
  {if $rowNumber < 2}
    </fieldset>
  {/if}
  {/section}
</div>
{foreach from=$beforeStaffCustomFields item=field}
  {assign var=fieldName value="custom_$field"}
  <div class="crm-section edit-row-custom_{$field}">
    <div class="label">{$form.$fieldName.label}</div>
    <div class="content">{$form.$fieldName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}
<div class="crm-public-form-item crm-section">
  {section name='s' start=1 loop=11}
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
<p>{ts}For each staff person who is a regulated professional, add a link to their listing on their College's site showing their status. If a url directly to the record is not available a link to the regulator's site is sufficient. For a camp, link to the camp's accreditation. Staff information is used by Autism Ontario for verification purposes and is not displayed to the public{/ts}</p>
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
  <div id="camp_session-{$rowN}" class="crm-section camp-section camp-section-{$rowN} {if $rowN > 1}hiddenElement{/if} {cycle values="odd-row,even-row"}">
    <div class="label">{$form.custom_13.$rowN.label}</div>
    <div class="content">{$form.custom_13.$rowN.html}</div>
    <div class="clear"></div>
    <div class="label">{ts}Camp Session Dates{/ts}</div>
    <div class="content">
      <div style="float:left;">{$form.custom_14.$rowN.label}<br>{$form.custom_14.$rowN.html}</div>
      <div>{$form.custom_15.$rowN.label}<br>{$form.custom_15.$rowN.html}</div>
    </div>
    <div class="clear"></div>
  </div>
{/section}

<p>{ts}Please note Autism Ontario reserves the right to refuse, suspend, or remove an applicant or previously approved member of Autism OneStop Listing Service{/ts}</p>
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

      if ($('#custom_863_3').prop('checked')) {
        $('[id^=custom_858]').each(function() {
          if ($(this).parent().text().length < 2) {
            $(this).parent().parent().parent().addClass('hiddenElement');
          }
        });
      }
      else {
       $('[id^=custom_858]').each(function() {
          $(this).parent().parent().parent().addClass('hiddenElement');
        });
      }
      $('[id^=staff_first_name_]').each(function() {
        if ($(this).parent().text().length < 2) {
          $(this).parent().parent().parent().parent().addClass('hiddenElement');
        }
      });
      $('[id^=work_address_]').each(function() {
        if ($(this).parent().text().length < 2) {
          $(this).parent().parent().parent().addClass('hiddenElement');
        }
      });
    });
  </script>
{/literal}
{/crmScope}
