# WhatsApp Order Automation (AutoModz.pk)

This guide explains how to configure Meta **WhatsApp Cloud API** for:

1. **Order confirmation** messages after checkout / payment  
2. **Customer confirmation** via WhatsApp reply (order moves to **processing** = ready for shipment)  
3. **Shipped / tracking** messages when admin sets order status to **shipped**

---

## 1. Prerequisites

- [Meta Business Portfolio](https://business.facebook.com/)
- [Meta Developer App](https://developers.facebook.com/) with **WhatsApp** product added
- A WhatsApp Business phone number connected to Cloud API
- Public HTTPS site (required for webhooks), e.g. `https://automodz.pk`

---

## 2. Meta Developer setup

### 2.1 Create / open your app

1. Go to [developers.facebook.com](https://developers.facebook.com/) → **My Apps** → create app (type: Business) or use existing.
2. Add product **WhatsApp** → **Set up**.

### 2.2 Get credentials

From **WhatsApp → API Setup**:

| Credential | Used in admin as |
|------------|------------------|
| **Phone number ID** | Phone Number ID |
| **WhatsApp Business Account ID** | (reference only) |
| **Temporary / permanent access token** | Permanent access token |

Generate a **System User** permanent token in Business Settings for production (recommended).

From **App settings → Basic**:

| Credential | Used in admin as |
|------------|------------------|
| **App Secret** | App secret (webhook signature verification) |

### 2.3 Configure webhook

1. In the app, open **WhatsApp → Configuration**.
2. **Callback URL**:  
   `https://YOUR-DOMAIN/webhooks/whatsapp`  
   (Shown on **Admin → WhatsApp Automation**.)
3. **Verify token**: copy from admin panel (or set your own and save in admin).
4. Click **Verify and save**.
5. Subscribe to webhook field: **`messages`**.

---

## 3. Admin panel configuration

1. Log in as admin → **WhatsApp Automation**.
2. Enable **WhatsApp automation**.
3. Paste **Phone Number ID**, **Access token**, **Verify token**, and **App secret**.
4. Toggle automation options:
   - **Send order confirmation** — message after order is created (or marked paid).
   - **Require customer WhatsApp confirm** — customer must reply to confirm; order status becomes **processing**.
   - **Send shipped / tracking update** — message when order status changes to **shipped**.
5. (Optional) **Template names** — if approved in Meta, use template mode (recommended for production).

Save settings.

---

## 4. Message flows

### 4.1 Order confirmation (outbound)

**Trigger:** New order from checkout, or order marked **paid** (Stripe / JazzCash / PayPal / manual).

**Customer receives:** Order number, total, and instruction:

```text
Reply CONFIRM AP-XXXXXXXX to confirm your order for shipment.
```

If **Order confirmation template** is set, a Meta template is sent instead (body parameters: customer name, order number, total).

### 4.2 Customer confirmation (inbound)

**Trigger:** Customer sends WhatsApp message to your business number.

**Accepted replies:**

- `CONFIRM AP-XXXXXXXX` (includes order number)
- `CONFIRM`, `YES`, `OK` (matches latest pending order for that phone)
- Interactive button payloads (if you use templates with buttons)

**System action:**

- Sets `whatsapp_confirmed_at` on the order
- If status is `pending` or `confirmed`, updates status to **`processing`** (ready for shipment)
- Sends a short thank-you reply on WhatsApp

**Phone matching:** Customer phone on the order is normalized to Pakistan format (`92…`) and matched to WhatsApp `from` id.

### 4.3 Shipped / tracking (outbound)

**Trigger:** Admin updates order status to **`shipped`** on **Admin → Orders → Order detail**.

**Customer receives:** Tracking ID and order tracking link.

- **Tracking ID:** `tracking_number` field if set on the order; otherwise internal tracking code derived from order number.
- **Link:** Guest order confirmation URL (with guest token).

Set **Courier tracking number** on the order before marking shipped for courier-specific IDs.

If **Shipping update template** is set, Meta template is used (parameters: order number, tracking id, track URL).

---

## 5. WhatsApp templates (production)

Outside the 24-hour customer service window, Meta requires **approved message templates** for business-initiated messages.

Suggested templates (create in **WhatsApp Manager → Message templates**):

### `automodz_order_confirmation` (example name)

- **Category:** Utility  
- **Language:** English  
- **Body:**  
  `Hi {{1}}, we received order {{2}} totaling {{3}}. Reply CONFIRM {{2}} to confirm shipment.`

Admin **Order confirmation template name:** `automodz_order_confirmation`

### `automodz_order_shipped` (example name)

- **Body:**  
  `Order {{1}} has shipped. Tracking: {{2}}. Track: {{3}}`

Admin **Shipping update template name:** `automodz_order_shipped`

If template names are left empty, the app sends **session text messages** (works for testing; may fail policy outside 24h window).

---

## 6. Database fields (orders)

| Column | Purpose |
|--------|---------|
| `whatsapp_confirmation_sent_at` | Confirmation message sent |
| `whatsapp_confirmed_at` | Customer confirmed via WhatsApp |
| `whatsapp_shipped_sent_at` | Shipped notification sent |
| `tracking_number` | Optional courier tracking (admin) |

---

## 7. Deployment checklist

```bash
php artisan migrate
```

1. Configure Meta webhook (section 2.3).  
2. Configure admin settings (section 3).  
3. Place a test order with a real mobile number on WhatsApp.  
4. Confirm reply moves order to **processing**.  
5. Set order to **shipped** and verify tracking message.

---

## 8. Troubleshooting

| Issue | Check |
|-------|--------|
| Webhook verify fails | Verify token matches admin + Meta config; URL is HTTPS |
| No confirmation sent | Automation enabled; phone number ID + token set; customer phone valid |
| Customer reply ignored | Webhook subscribed to `messages`; phone on order matches WhatsApp number |
| Template send fails | Template name/language approved in Meta; parameter count matches |
| 403 on webhook POST | App secret correct; signature header present |

Logs: Laravel log channel (`storage/logs`) — search for `WhatsApp`.

---

## 9. Security notes

- Access token and app secret are **encrypted** in the database.  
- Webhook route is excluded from CSRF; **app secret** signature verification is strongly recommended.  
- Do not commit tokens to git; use admin panel or server env for bootstrap only.

---

## 10. Support link on storefront

The floating WhatsApp button (`site.whatsapp` in config) is separate from Cloud API automation but should use the **same business number** customers expect replies from.
