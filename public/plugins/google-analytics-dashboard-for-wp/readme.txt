=== Google Analytics Dashboard for WP by ExactMetrics (formerly GADWP) ===
Contributors: chriscct7, smub
Donate link: http://www.wpbeginner.com/wpbeginner-needs-your-help/
Tags: analytics,google analytics,google analytics dashboard,google analytics plugin,google analytics widget,gtag
Requires at least: 3.5
Tested up to: 4.9
Stable tag: 5.3.5
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connects Google Analytics with your WordPress site. Displays stats to help you understand your users and site content on a whole new level!

== Description ==
This Google Analytics for WordPress plugin enables you to track your site using the latest Google Analytics tracking code and allows you to view key Google Analytics stats in your WordPress install.

In addition to a set of general Google Analytics stats, in-depth Page reports and in-depth Post reports allow further segmentation of your analytics data, providing performance details for each post or page from your website.

The Google Analytics tracking code is fully customizable through options and hooks, allowing advanced data collection like custom dimensions and events.

= Google Analytics Real-Time Stats =

Google Analytics reports, in real-time, in your dashboard screen:

- Real-time number of visitors 
- Real-time acquisition channels
- Real-time traffic sources details 

= Google Analytics Reports =

The Google Analytics reports you need, on your dashboard, in your All Posts and All Pages screens, and on site's frontend:  

- Sessions, organic searches, page views, bounce rate analytics stats
- Locations, pages, referrers, keywords, 404 errors analytics stats
- Traffic channels, social networks, traffic mediums, search engines analytics stats
- Device categories, browsers, operating systems, screen resolutions, mobile brands analytics stats

In addition, you can control who can view specific Google Analytics reports by setting permissions based on user roles.

= Google Analytics Tracking =

Installs the latest Google Analytics tracking code and allows full code customization:

- Universal Google Analytics (analytics.js) tracking code
- Global Site Tag (gtag.js) tracking code
- Enhanced link attribution
- Remarketing, demographics and interests tracking
- Page Speed sampling rate control
- User sampling rate control
- Cross domain tracking
- Exclude user roles from tracking
- Accelerated Mobile Pages (AMP) support for Google Analytics
- Ecommerce support for Google Analytics

User privacy oriented features:

- IP address anonymization
- option to follow Do Not Track (DNT) sent by browsers
- support for user tracking opt-out

Google Analytics Dashboard for WP enables you to easily track events like:
 
- Downloads
- Emails 
- Outbound links
- Affiliate links
- Fragment identifiers
- Telephone
- Page Scrolling Depth
- Custom event categories, actions and labels using annotated HTML elements

With Google Analytics Dashboard for WP you can use custom dimensions to track:

- Authors
- Publication year
- Publication month
- Categories
- Tags
- User engagement

Actions and filters are available for further Google Analytics tracking code customization.

= Google Tag Manager Tracking =

As an alternative to Google Analytics tracking code, you can use Google Tag Manager for tracking:

- Google Tag Manager code
- Data Layer variables: authors, publication year, publication month, categories, tags, user type
- Exclude user roles from tracking
- Accelerated Mobile Pages (AMP) support for Google Tag Manager

= Accelerated Mobile Pages (AMP) features =

- Google Tag Manager basic tracking
- Google Analytics basic tracking 
- Automatically removes <em>amp/</em> from Google Analytics tracking page URL
- Scrolling depth tracking
- Custom dimensions tracking
- User sampling rate control
- Form submit tracking
- File downloads tracking
- Affiliate links tracking
- Hashmarks, outbound links, telephones and e-mails tracking
- Custom event categories, actions and labels using annotated HTML elements

= Google Analytics Dashboard for WP on Multisite =

This plugin is fully compatible with multisite network installs, allowing three setup modes:

- Mode 1: network activated using multiple Google Analytics accounts
- Mode 2: network activated using a single Google Analytics account
- Mode 3: network deactivated using multiple Google Analytics accounts

> <strong>Google Analytics Dashboard for WP on GitHub</strong><br>
> You can submit feature requests or bugs on [the Google Analytics Dashboard for WP by ExactMetrics Github repository](https://github.com/awesomemotive/Google-Analytics-Dashboard-for-WP).

== Installation ==

1. Upload the full google-analytics-dashboard-for-wp directory into your wp-content/plugins directory.
2. In WordPress select Plugins from your sidebar menu and activate the Google Analytics Dashboard for WP plugin.
3. Open the plugin configuration page, which is located under Google Analytics menu.
4. Authorize the plugin to connect to Google Analytics using the Authorize Plugin button.
5. Go back to the plugin configuration page, which is located under Google Analytics menu to update/set your settings.
6. Go to Google Analytics -> Tracking Code to configure/enable/disable tracking.

== Frequently Asked Questions == 

= Do I have to insert the Google Analytics tracking code manually? =

No, once the plugin is authorized and a default domain is selected the Google Analytics tracking code is automatically inserted in all webpages.

= Some settings are missing in the video tutorial =

We are constantly improving Google Analytics Dashboard for WP, sometimes the video tutorial may be a little outdated.

= How can I suggest a new feature, contribute or report a bug? =

You can submit pull requests, feature requests and bug reports on [our GitHub repository](https://github.com/awesomemotive/Google-Analytics-Dashboard-for-WP).

= Documentation, Tutorials and FAQ =

For documentation, tutorials, FAQ and videos check out: [Google Analytics Dashboard for WP by ExactMetrics documentation](https://exactmetrics.com/).

== Screenshots ==

1. Google Analytics Dashboard for WP Blue Color
2. Google Analytics Dashboard for WP Real-Time
3. Google Analytics Dashboard for WP reports per Posts/Pages
4. Google Analytics Dashboard for WP Geo Map
5. Google Analytics Dashboard for WP Top Pages, Top Referrers and Top Searches
6. Google Analytics Dashboard for WP Traffic Overview
7. Google Analytics Dashboard for WP statistics per page on Frontend
8. Google Analytics Dashboard for WP cities on region map
9. Google Analytics Dashboard for WP Widget

== Localization ==

You can translate Google Analytics Dashboard for WP on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/google-analytics-dashboard-for-wp).

== License ==

Google Analytics Dashboard for WP it's released under the GPLv2, you can use it free of charge on your personal or commercial website.

== Upgrade Notice ==

== Changelog ==

[GADWP v5.3 release notes](https://exactmetrics.com/adding-gtag-js-to-your-site/)

= 5.3.5 =
* Bug Fixes:
	* Re-tagging release to fix a deployment issue.

= 5.3.4 =
* Enhancements:
	* Adds more robust settings to control various ExactMetrics configuration warnings.
	* Adds the ability to opt-into usage tracking.
	

= 5.3.3 =
* Bug Fixes:
	* Updated endpoint for GA auth to use updated system.
	* Fixed a bug where the opt-out and exclude DNT options were listed twice.

= 5.3.2 =
* Bug Fixes:	
	* fixes for user opt-out feature 
* Enhancements: 
	* use <em>gadwp_useroptout</em> shortcode to easily generate opt-out buttons and links, [more details](https://exactmetrics.com/google-analytics-gdpr-and-user-data-privacy-compliance)
	* adding <em>gadwp_gtag_commands</em> and <em>gadwp_gtag_script_path</em> hooks to allow further gtag (Global Site Tag) code customization
	* adds opt-out and DNT support for Google Tag Manager	
	
= 5.3.1.1 =
* Bug Fixes:	
	* avoid tracking issues by not clearing the profiles list on automatic token resets

= 5.3.1 =
* Bug Fixes:	
	* frontend_item_reports PHP notice when upgrading from a version lower than v4.8.0.1   

= 5.3 =
* Enhancements: 
	* adds full support for Global Site Tag (gtag.js)
	* remove Scroll Depth functionality, since this is now available as a trigger on Google Tag Manager
	* adds custom dimensions support for AMP pages with Google Tag Manager tracking
	* adds support for button submits
* Bug Fixes:	
	* form submit events were not following the non-interaction settings   
	
= 5.2.3.1 =
* Bug Fixes:	
	* fixing a small reporting issue 
	
= 5.2.3 =
* Enhancements:
	* add Google Analytics user opt-out support
	* add option to exclude tracking for users sending the <em>Do Not Track</em> header
	* add System tab to Errors & Debug screen
	* check to avoid using a redeemed access code
* Bug Fixes:	
	* remove a debugging message
	* cURL options were overwritten during regular API calls	

= 5.2.2 =
* Enhancements:  
	* more informative alerts and suggestions on the authorization screen
	* disable autocomplete for the access code input field to avoid reuse of the same unique authorization code
	* GADWP Endpoint improvements
	* Error reporting improvements
	* introducing the gadwp_maps_api_key filter
* Bug Fixes:	
	* use the theme color palette for the frontend widget 	 

= 5.2.1 =
* Enhancements:  
	* avoid submitting empty error reports
* Bug Fixes:	
	* fixes a bug for custom PHP cURL options 
	
= 5.2 =
* Enhancements:  
	* improvements on exponential backoff system
	* introduces a new authentication method with endpoints
	* multiple updates of plugin's options
	* code cleanup
	* improvements on error reporting system
	* option to report errors to developer
	* move the upgrade notice from the Dashboard to plugin's settings page
	* enable PHP cURL proxy support using WordPress settings, props by [Joe Hobson](https://github.com/joehobson)
	* hide unusable options based on plugin's settings 
* Bug Fixes:	
	* some thrown errors were not displayed on Errors & Debug screen
	* analytics icon disappears from post list after quick edit, props by [karex](https://github.com/karex)
	* fix for inline SVG links, props by [Andrew Minion](https://github.com/macbookandrew)
	* fixes a bug on affiliate events tracking

The full changelog is [available here](https://exactmetrics.com/changelog-google-analytics-dashboard-for-wp/).
