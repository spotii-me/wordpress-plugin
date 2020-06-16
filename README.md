# wordpress-plugin
Spotii plugin for Wordpress

# WooCommerce
This extension allows you to use Spotii as a payment gateway in your WooCommerce store.

Ensure you have signed up as a merchant on Spotii

# 1. Installation steps
Get folder ‘spotii-gateway’ from plugin zip
Copy this folder inside /Wordpress [ROOT]/wp-content/plugins/
Ensure folder structure as /wp-content/plugins/spotii-gateway/spotii-gateway.php
# 2a. Admin Configuration
Login to your Wordpress Admin portal
Navigate to Plugins > Installed Plugins
Look for “Spotii Payment Gateway” in the plugins list and click “Install”
On installation complete, click on “Activate”
# 2b. Payment Setup
Navigate to WooCommerce > Settings > Payments
Check radio button “Enable” and click on “Set up” on Spotii Gateway
Enable Spotii gateway
Enable test mode – disable this when going live
Put your test (staging) private and public keys in Test fields
Put your live (staging) keys in Live fields
Save changes
