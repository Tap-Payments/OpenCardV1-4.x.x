# OpenCart 4.x Payment Plugin - Tap payments

This is a payment extension module for OpenCart 4.x that integrates the Tap payments with your OpenCart store. 
---

## 🧩 Features

- Seamless integration with OpenCart 4.x
- Customizable via admin panel
- Secure transactions 

---

## 🔧 Installation
1. **Download Files**

	-Downlaod zip fronm github and rename it to tap.ocmod.zip

1. **Upload Files**
   - Go to installer from corner click upload and choose the renamed zip file that is tap.ocmod.zip .

2. **Refresh Modifications**
   - Go to `Extensions > Modifications` and click **Refresh** (⟳ button in the top right).

3. **Install the Extension**
   - Go to `Extensions > Extensions > Payments`
   - Locate **[Tap Payments]** and click **Install**

4. **Configure**
   - Click **Edit** next to the installed module.
   - Enter Tao API credentials (provided by Tap payments).
   - Set required options (test/live mode, order statuses, geo zones, etc.)
   - Save your settings.

---

## 🔐 Configuration Fields

| Field | Description |
|-------|-------------|
| API Test Secret Key | Provided by Tap payment gateway |
| API Live Secret Key | Provided by Tap payment gateway |
| API Live Secret Key | Provided by Tap payment gateway |
| Environment | Choose between Test and Live |
| Charge mode | Charge or Authorize |
| Order Status (Success) | Status assigned to successful orders |
| Geo Zone | Restrict to specific regions (optional) |
| Status | Enable/Disable the module |


---


