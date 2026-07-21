# Standard SOP to follow for Database Schema

## Primary Keys
- Use INT UNSIGNED for all primary keys by default.
- Avoid BIGINT FOR CRM/ERP, will use in IOT solutions in future if required. But till than focus on INT Only.
- Use BIGINT UNSIGNED only for very large tables expected to exceed 429 crores rows (e.g., log, sensorData).

## Financial Values, Money, Amount etc.
- Use DECIMAL(18,2) for all currency/financial fields. 	This ensures high precision and future-proofing (e.g., totalAmount, taxAmount, invoiceValue).

## Other Numeric but possible in point values, like: Quantities / Percentages / Rates
- Use DECIMAL(10,2) for stock quantities, percentages (e.g., GST slabs), unit rates, dimensions, etc.
- Avoid FLOAT/DOUBLE to prevent rounding errors during calculation and math operations.

## mobile/whatsapp/contact number: 
- Always use VARCHAR(20) field.

## email:
- Always use  VARCHAR(191) field.

## TenantId
- tenantId: should be everywhere in all tables, only not in global saas level master tables like locationMaster, countryMaster.

## Which Table should have isActive (Boolean) field?
- Add to all reference/config/master tables (e.g., userMaster, itemCategory, taxMaster).
- Use to enable/disable records operationally.
- Do not use in transactional tables like leads, quotations, pi, orders, tasks, etc.

## Which Table should have isDeleted (Boolean) field?
- Add to all transactional tables for soft delete (e.g., invoiceMaster, paymentEntry, jobcardMaster).
- Also use in master/config tables where user-initiated soft delete is allowed. Ask before using here.

