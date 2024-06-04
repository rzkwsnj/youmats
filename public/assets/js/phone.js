/**
* ====================

*  ☎️ Browser Phone ☎️
* ====================
* A fully featured browser based WebRTC SIP phone for Asterisk
* -------------------------------------------------------------
*  Copyright (c) 2020  - Conrad de Wet - All Rights Reserved.
* =============================================================
* File: phone.js
* License: GNU Affero General Public License v3.0
* Owner: Conrad de Wet
* Date: April 2020
* Git: https://github.com/InnovateAsterisk/Browser-Phone
*/

// Global Settings
// ===============
const appversion = "0.3.23";
const sipjsversion = "0.20.0";
const navUserAgent = window.navigator.userAgent;  // TODO: change to Navigator.userAgentData

/**
 * Language Packs (lang/xx.json)
 * Note: The following should correspond to files on your server.
 * eg: If you list "fr" then you need to add the file "fr.json".
 * Use the "en.json" as a template.
 * More specific language must be first. ie: "zh-hans" should be before "zh".
 * "en.json" is always loaded by default
 */
let loadAlternateLang = (getDbItem("loadAlternateLang", "0") == "1"); // Enables searching and loading for the additional language packs other thAan /en.json
const availableLang = ["ja", "zh-hans", "zh", "ru", "tr", "nl", "es", "de", "pl", "pt-br"]; // Defines the language packs (.json) available in /lang/ folder

/**
 * Image Assets
 * Note: You can specify the assets to use below in array format
 */
let imagesDirectory = getDbItem("imagesDirectory", "");     // Directory For Image Assets eg: images/
let defaultAvatars = getDbItem("defaultAvatars", "avatars/default.0.webp,avatars/default.1.webp,avatars/default.2.webp,avatars/default.3.webp,avatars/default.4.webp,avatars/default.5.webp,avatars/default.6.webp,avatars/default.7.webp,avatars/default.8.webp");
let wallpaperLight = getDbItem("wallpaperLight", "wallpaper.light.webp");  // Wallpaper for Light Theme
let wallpaperDark = getDbItem("wallpaperDark", "wallpaper.dark.webp");     // Wallpaper for Dark Theme

/**
 *
 * User Settings & Defaults
 * Note: Generally you don't really need to be changing these settings, the defaults should be fine
 * If you want to  keep this library in its original form, but still provision settings, look at the
 * index.html for some sample provisioning and web_hook options.
 */
let profileUserID = getDbItem("profileUserID", null);   // Internal reference ID. (DON'T CHANGE THIS!)
let profileName = getDbItem("profileName", null);       // eg: Keyla James
let wssServer = getDbItem("wssServer", null);           // eg: raspberrypi.local
let WebSocketPort = getDbItem("WebSocketPort", null);   // eg: 444 | 4443
let ServerPath = getDbItem("ServerPath", null);         // eg: /ws
let SipDomain = getDbItem("SipDomain", null);           // eg: raspberrypi.local
let SipUsername = getDbItem("SipUsername", null);       // eg: webrtc
let SipPassword = getDbItem("SipPassword", null);       // eg: webrtc

let SingleInstance = (getDbItem("SingleInstance", "1") == "1");      // Un-registers this account if the phone is opened in another tab/window

let TransportConnectionTimeout = parseInt(getDbItem("TransportConnectionTimeout", 15));          // The timeout in seconds for the initial connection to make on the web socket port
let TransportReconnectionAttempts = parseInt(getDbItem("TransportReconnectionAttempts", 999));   // The number of times to attempt to reconnect to a WebSocket when the connection drops.
let TransportReconnectionTimeout = parseInt(getDbItem("TransportReconnectionTimeout", 3));       // The time in seconds to wait between WebSocket reconnection attempts.

let SubscribeToYourself = (getDbItem("SubscribeToYourself", "0") == "1");              // Enable Subscribe to your own uri. (Useful to understand how other buddies see you.)
let VoiceMailSubscribe = (getDbItem("VoiceMailSubscribe", "1") == "1");                // Enable Subscribe to voicemail
let VoicemailDid = getDbItem("VoicemailDid", "");                                      // Number to dial for VoicemialMain()
let SubscribeVoicemailExpires = parseInt(getDbItem("SubscribeVoicemailExpires", 300)); // Voceimail Subscription expiry time (in seconds)
let ContactUserName = getDbItem("ContactUserName", "");                                // Optional name for contact header uri
let userAgentStr = getDbItem("UserAgentStr", "Browser Phone "+ appversion +" (SIPJS - "+ sipjsversion +") "+ navUserAgent);   // Set this to whatever you want.
let hostingPrefix = getDbItem("HostingPrefix", "");                                    // Use if hosting off root directory. eg: "/phone/" or "/static/"
let RegisterExpires = parseInt(getDbItem("RegisterExpires", 300));                     // Registration expiry time (in seconds)
let RegisterExtraHeaders = getDbItem("RegisterExtraHeaders", "{}");                    // Parsable Json string of headers to include in register process. eg: '{"foo":"bar"}'
let RegisterExtraContactParams = getDbItem("RegisterExtraContactParams", "{}");        // Parsable Json string of extra parameters add to the end (after >) of contact header during register. eg: '{"foo":"bar"}'
let RegisterContactParams = getDbItem("RegisterContactParams", "{}");                  // Parsable Json string of extra parameters added to contact URI during register. eg: '{"foo":"bar"}'
let WssInTransport = (getDbItem("WssInTransport", "1") == "1");                        // Set the transport parameter to wss when used in SIP URIs. (Required for Asterisk as it doesn't support Path)
let IpInContact = (getDbItem("IpInContact", "1") == "1");                              // Set a random IP address as the host value in the Contact header field and Via sent-by parameter. (Suggested for Asterisk)
let BundlePolicy = getDbItem("BundlePolicy", "balanced");                              // SDP Media Bundle: max-bundle | max-compat | balanced https://webrtcstandards.info/sdp-bundle/
let IceStunServerJson = getDbItem("IceStunServerJson", "");                            // Sets the JSON string for ice Server. Default: [{ "urls": "stun:stun.l.google.com:19302" }] Must be https://developer.mozilla.org/en-US/docs/Web/API/RTCConfiguration/iceServers
let IceStunCheckTimeout = parseInt(getDbItem("IceStunCheckTimeout", 500));             // Set amount of time in milliseconds to wait for the ICE/STUN server
let SubscribeBuddyAccept = getDbItem("SubscribeBuddyAccept", "application/pidf+xml");  // Normally only application/dialog-info+xml and application/pidf+xml
let SubscribeBuddyEvent = getDbItem("SubscribeBuddyEvent", "presence");                // For application/pidf+xml use presence. For application/dialog-info+xml use dialog
let SubscribeBuddyExpires = parseInt(getDbItem("SubscribeBuddyExpires", 300));         // Buddy Subscription expiry time (in seconds)
let profileDisplayPrefix = getDbItem("profileDisplayPrefix", "");                      // Can display an item from you vCard before you name. Options: Number1 | Number2
let profileDisplayPrefixSeparator = getDbItem("profileDisplayPrefixSeparator", "");    // Used with profileDisplayPrefix, adds a separating character (string). eg: - ~ * or even 💥

let NoAnswerTimeout = parseInt(getDbItem("NoAnswerTimeout", 120));          // Time in seconds before automatic Busy Here sent
let AutoAnswerEnabled = (getDbItem("AutoAnswerEnabled", "0") == "1");       // Automatically answers the phone when the call comes in, if you are not on a call already
let DoNotDisturbEnabled = (getDbItem("DoNotDisturbEnabled", "0") == "1");   // Rejects any inbound call, while allowing outbound calls
let CallWaitingEnabled = (getDbItem("CallWaitingEnabled", "1") == "1");     // Rejects any inbound call if you are on a call already.
let RecordAllCalls = (getDbItem("RecordAllCalls", "0") == "1");             // Starts Call Recording when a call is established.
let StartVideoFullScreen = (getDbItem("StartVideoFullScreen", "1") == "1"); // Starts a video call in the full screen (browser screen, not desktop)
let SelectRingingLine = (getDbItem("SelectRingingLine", "1") == "1");       // Selects the ringing line if you are not on another call ()

let UiMaxWidth = parseInt(getDbItem("UiMaxWidth", 1240));                                   // Sets the max-width for the UI elements (don't set this less than 920. Set to very high number for full screen eg: 999999)
let UiThemeStyle = getDbItem("UiThemeStyle", "system");                                     // Sets the color theme for the UI dark | light | system (set by your systems dark/light settings)
let UiMessageLayout = getDbItem("UiMessageLayout", "middle");                               // Put the message Stream at the top or middle can be either: top | middle
let UiCustomConfigMenu = (getDbItem("UiCustomConfigMenu", "0") == "1");                     // If set to true, will only call web_hook_on_config_menu
let UiCustomDialButton = (getDbItem("UiCustomDialButton", "0") == "1");                     // If set to true, will only call web_hook_dial_out
let UiCustomSortAndFilterButton = (getDbItem("UiCustomSortAndFilterButton", "0") == "1");   // If set to true, will only call web_hook_sort_and_filter
let UiCustomAddBuddy = (getDbItem("UiCustomAddBuddy", "0") == "1");                         // If set to true, will only call web_hook_on_add_buddy
let UiCustomEditBuddy = (getDbItem("UiCustomEditBuddy", "0") == "1");                       // If set to true, will only call web_hook_on_edit_buddy({})
let UiCustomMediaSettings = (getDbItem("UiCustomMediaSettings", "0") == "1");               // If set to true, will only call web_hook_on_edit_media
let UiCustomMessageAction = (getDbItem("UiCustomMessageAction", "0") == "1");               // If set to true, will only call web_hook_on_message_action

let AutoGainControl = (getDbItem("AutoGainControl", "1") == "1");        // Attempts to adjust the microphone volume to a good audio level. (OS may be better at this)
let EchoCancellation = (getDbItem("EchoCancellation", "1") == "1");      // Attempts to remove echo over the line.
let NoiseSuppression = (getDbItem("NoiseSuppression", "1") == "1");      // Attempts to clear the call quality of noise.
let MirrorVideo = getDbItem("VideoOrientation", "rotateY(180deg)");      // Displays the self-preview in normal or mirror view, to better present the preview.
let maxFrameRate = getDbItem("FrameRate", "");                           // Suggests a frame rate to your webcam if possible.
let videoHeight = getDbItem("VideoHeight", "");                          // Suggests a video height (and therefor picture quality) to your webcam.
let MaxVideoBandwidth = parseInt(getDbItem("MaxVideoBandwidth", "2048")); // Specifies the maximum bandwidth (in Kb/s) for your outgoing video stream. e.g: 32 | 64 | 128 | 256 | 512 | 1024 | 2048 | -1 to disable
let videoAspectRatio = getDbItem("AspectRatio", "1.33");                  // Suggests an aspect ratio (1:1 = 1 | 4:3 = 0.75 | 16:9 = 0.5625) to your webcam.
let NotificationsActive = (getDbItem("Notifications", "0") == "1");

let StreamBuffer = parseInt(getDbItem("StreamBuffer", 50));                 // The amount of rows to buffer in the Buddy Stream
let MaxDataStoreDays = parseInt(getDbItem("MaxDataStoreDays", 0));          // Defines the maximum amount of days worth of data (calls, recordings, messages, etc) to store locally. 0=Stores all data always. >0 Trims n days back worth of data at various events where.
let PosterJpegQuality = parseFloat(getDbItem("PosterJpegQuality", 0.6));    // The image quality of the Video Poster images
let VideoResampleSize = getDbItem("VideoResampleSize", "HD");               // The resample size (height) to re-render video that gets presented (sent). (SD = ???x360 | HD = ???x720 | FHD = ???x1080)
let RecordingVideoSize = getDbItem("RecordingVideoSize", "HD");             // The size/quality of the video track in the recordings (SD = 640x360 | HD = 1280x720 | FHD = 1920x1080)
let RecordingVideoFps = parseInt(getDbItem("RecordingVideoFps", 12));       // The Frame Per Second of the Video Track recording
let RecordingLayout = getDbItem("RecordingLayout", "them-pnp");             // The Layout of the Recording Video Track (side-by-side | them-pnp | us-only | them-only)

let DidLength = parseInt(getDbItem("DidLength", 6));                 // DID length from which to decide if an incoming caller is a "contact" or an "extension".
let MaxDidLength = parseInt(getDbItem("MaxDidLength", 16));          // Maximum length of any DID number including international dialled numbers.
let DisplayDateFormat = getDbItem("DateFormat", "YYYY-MM-DD");       // The display format for all dates. https://momentjs.com/docs/#/displaying/
let DisplayTimeFormat = getDbItem("TimeFormat", "h:mm:ss A");        // The display format for all times. https://momentjs.com/docs/#/displaying/
let Language = getDbItem("Language", "auto");                        // Overrides the language selector or "automatic". Must be one of availableLang[]. If not defaults to en.

// Buddy Sort and Filter
let BuddySortBy = getDbItem("BuddySortBy", "activity");                      // Sorting for Buddy List display (type|extension|alphabetical|activity)
let SortByTypeOrder = getDbItem("SortByTypeOrder", "e|x|c");                 // If the Sorting is set to type then describe the order of the types.
let BuddyAutoDeleteAtEnd = (getDbItem("BuddyAutoDeleteAtEnd", "0") == "1");  // Always put the Auto Delete buddies at the bottom
let HideAutoDeleteBuddies = (getDbItem("HideAutoDeleteBuddies", "0") == "1");    // Option to not display Auto Delete Buddies (May be confusing if newly created buddies are set to auto delete.)
let BuddyShowExtenNum = (getDbItem("BuddyShowExtenNum", "0") == "1");        // Controls the Extension Number display

// Permission Settings
let EnableTextMessaging = (getDbItem("EnableTextMessaging", "1") == "1");               // Enables the Text Messaging
let DisableFreeDial = (getDbItem("DisableFreeDial", "0") == "1");                       // Removes the Dial icon in the profile area, users will need to add buddies in order to dial.
let DisableBuddies = (getDbItem("DisableBuddies", "0") == "1");                         // Removes the Add Someone menu item and icon from the profile area. Buddies will still be created automatically. Please also use MaxBuddies or MaxBuddyAge
let EnableTransfer = (getDbItem("EnableTransfer", "1") == "1");                         // Controls Transferring during a call
let EnableConference = (getDbItem("EnableConference", "1") == "1");                     // Controls Conference during a call
let AutoAnswerPolicy = getDbItem("AutoAnswerPolicy", "allow");                          // allow = user can choose | disabled = feature is disabled | enabled = feature is always on
let DoNotDisturbPolicy = getDbItem("DoNotDisturbPolicy", "allow");                      // allow = user can choose | disabled = feature is disabled | enabled = feature is always on
let CallWaitingPolicy = getDbItem("CallWaitingPolicy", "allow");                        // allow = user can choose | disabled = feature is disabled | enabled = feature is always on
let CallRecordingPolicy = getDbItem("CallRecordingPolicy", "allow");                    // allow = user can choose | disabled = feature is disabled | enabled = feature is always on
let IntercomPolicy = getDbItem("IntercomPolicy", "enabled");                            // disabled = feature is disabled | enabled = feature is always on
let EnableAccountSettings = (getDbItem("EnableAccountSettings", "1") == "1");           // Controls the Account tab in Settings
let EnableAppearanceSettings = (getDbItem("EnableAppearanceSettings", "1") == "0");     // Controls the Appearance tab in Settings
let EnableNotificationSettings = (getDbItem("EnableNotificationSettings", "1") == "0"); // Controls the Notifications tab in Settings
let EnableAlphanumericDial = (getDbItem("EnableAlphanumericDial", "0") == "1");         // Allows calling /[^\da-zA-Z\*\#\+\-\_\.\!\~\'\(\)]/g default is /[^\d\*\#\+]/g
let EnableVideoCalling = (getDbItem("EnableVideoCalling", "1") == "0");                 // Enables Video during a call
let EnableTextExpressions = (getDbItem("EnableTextExpressions", "1") == "1");           // Enables Expressions (Emoji) glyphs when texting
let EnableTextDictate = (getDbItem("EnableTextDictate", "1") == "1");                   // Enables Dictate (speech-to-text) when texting
let EnableRingtone = (getDbItem("EnableRingtone", "1") == "1");                         // Enables a ring tone when an inbound call comes in.  (media/Ringtone_1.mp3)
let MaxBuddies = parseInt(getDbItem("MaxBuddies", 999));                                // Sets the Maximum number of buddies the system will accept. Older ones get deleted. (Considered when(after) adding buddies)
let MaxBuddyAge = parseInt(getDbItem("MaxBuddyAge", 365));                              // Sets the Maximum age in days (by latest activity). Older ones get deleted. (Considered when(after) adding buddies)
let AutoDeleteDefault = (getDbItem("AutoDeleteDefault", "1") == "1");                   // For automatically created buddies (inbound and outbound), should the buddy be set to AutoDelete.

let ChatEngine = getDbItem("ChatEngine", "SIMPLE");    // Select the chat engine XMPP | SIMPLE

// XMPP Settings
let XmppServer = getDbItem("XmppServer", "");                // FQDN of XMPP server HTTP service";
let XmppWebsocketPort = getDbItem("XmppWebsocketPort", "");  // OpenFire Default : 7443
let XmppWebsocketPath = getDbItem("XmppWebsocketPath", "");  // OpenFire Default : /ws
let XmppDomain = getDbItem("XmppDomain", "");                // The domain of the XMPP server
let profileUser = getDbItem("profileUser", null);            // Username for auth with XMPP Server eg: 100
// XMPP Tenanting
let XmppRealm = getDbItem("XmppRealm", "");                    // To create a tenant like partition in XMPP server all users and buddies will have this realm prepended to their details.
let XmppRealmSeparator = getDbItem("XmppRealmSeparator", "-"); // Separates the realm from the profileUser eg: abc123-100@XmppDomain
// TODO
let XmppChatGroupService = getDbItem("XmppChatGroupService", "conference");

// TODO
let EnableSendFiles = false;          // Enables sending of Images
let EnableSendImages = false;          // Enables sending of Images
let EnableAudioRecording = false;  // Enables the ability to record a voice message
let EnableVideoRecording = false;  // Enables the ability to record a video message
let EnableSms = false;             // Enables SMS sending to the server (requires onward services)
let EnableFax = false;             // Enables Fax sending to the server (requires onward services)
let EnableEmail = false;           // Enables Email sending to the server (requires onward services)

// ===================================================
// Rather don't fiddle with anything beyond this point
// ===================================================

// System variables
// ================
const instanceID = String(Date.now());
let localDB = window.localStorage;
let userAgent = null;
let CanvasCollection = [];
let Buddies = [];
let selectedBuddy = null;
let selectedLine = null;
let windowObj = null;
let alertObj = null;
let confirmObj = null;
let promptObj = null;
let menuObj = null;
let HasVideoDevice = false;
let HasAudioDevice = false;
let HasSpeakerDevice = false;
let AudioinputDevices = [];
let VideoinputDevices = [];
let SpeakerDevices = [];
let Lines = [];
let lang = {}
let audioBlobs = {}
let newLineNumber = 1;
let telNumericRegEx = /[^\d\*\#\+]/g
let telAlphanumericRegEx = /[^\da-zA-Z\*\#\+\-\_\.\!\~\'\(\)]/g

let settingsMicrophoneStream = null;
let settingsMicrophoneStreamTrack = null;
let settingsMicrophoneSoundMeter = null;

let settingsVideoStream = null;
let settingsVideoStreamTrack = null;

// Utilities
// =========
function uID(){
    return Date.now()+Math.floor(Math.random()*10000).toString(16).toUpperCase();
}
function utcDateNow(){
    return moment().utc().format("YYYY-MM-DD HH:mm:ss UTC");
}
function getDbItem(itemIndex, defaultValue){
    var localDB = window.localStorage;
    if(localDB.getItem(itemIndex) != null) return localDB.getItem(itemIndex);
    return defaultValue;
}
function getAudioSrcID(){
    var id = localDB.getItem("AudioSrcId");
    return (id != null)? id : "default";
}
function getAudioOutputID(){
    var id = localDB.getItem("AudioOutputId");
    return (id != null)? id : "default";
}
function getVideoSrcID(){
    var id = localDB.getItem("VideoSrcId");
    return (id != null)? id : "default";
}
function getRingerOutputID(){
    var id = localDB.getItem("RingOutputId");
    return (id != null)? id : "default";
}
function formatDuration(seconds){
    var sec = Math.floor(parseFloat(seconds));
    if(sec < 0){
        return sec;
    }
    else if(sec >= 0 && sec < 60){
        return sec + " " + ((sec > 1) ? lang.seconds_plural : lang.second_single);
    }
    else if(sec >= 60 && sec < 60 * 60){ // greater then a minute and less then an hour
        var duration = moment.duration(sec, 'seconds');
        return duration.minutes() + " "+ ((duration.minutes() > 1) ? lang.minutes_plural: lang.minute_single) +" " + duration.seconds() +" "+ ((duration.seconds() > 1) ? lang.seconds_plural : lang.second_single);
    }
    else if(sec >= 60 * 60 && sec < 24 * 60 * 60){ // greater than an hour and less then a day
        var duration = moment.duration(sec, 'seconds');
        return duration.hours() + " "+ ((duration.hours() > 1) ? lang.hours_plural : lang.hour_single) +" " + duration.minutes() + " "+ ((duration.minutes() > 1) ? lang.minutes_plural: lang.minute_single) +" " + duration.seconds() +" "+ ((duration.seconds() > 1) ? lang.seconds_plural : lang.second_single);
    }
    //  Otherwise.. this is just too long
}
function formatShortDuration(seconds){
    var sec = Math.floor(parseFloat(seconds));
    if(sec < 0){
        return sec;
    }
    else if(sec >= 0 && sec < 60){
        return "00:"+ ((sec > 9)? sec : "0"+sec );
    }
    else if(sec >= 60 && sec < 60 * 60){ // greater then a minute and less then an hour
        var duration = moment.duration(sec, 'seconds');
        return ((duration.minutes() > 9)? duration.minutes() : "0"+duration.minutes()) + ":" + ((duration.seconds() > 9)? duration.seconds() : "0"+duration.seconds());
    }
    else if(sec >= 60 * 60 && sec < 24 * 60 * 60){ // greater than an hour and less then a day
        var duration = moment.duration(sec, 'seconds');
        return ((duration.hours() > 9)? duration.hours() : "0"+duration.hours())  + ":" + ((duration.minutes() > 9)? duration.minutes() : "0"+duration.minutes())  + ":" + ((duration.seconds() > 9)? duration.seconds() : "0"+duration.seconds());
    }
    //  Otherwise.. this is just too long
}
function formatBytes(bytes, decimals) {
    if (bytes === 0) return "0 "+ lang.bytes;
    var k = 1024;
    var dm = (decimals && decimals >= 0)? decimals : 2;
    var sizes = [lang.bytes, lang.kb, lang.mb, lang.gb, lang.tb, lang.pb, lang.eb, lang.zb, lang.yb];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
}
function UserLocale(){
    var language = window.navigator.userLanguage || window.navigator.language; // "en", "en-US", "fr", "fr-FR", "es-ES", etc.
    // langtag = language["-"script]["-" region] *("-" variant) *("-" extension) ["-" privateuse]
    // TODO Needs work
    langtag = language.split('-');
    if(langtag.length == 1){
        return "";
    }
    else if(langtag.length == 2) {
        return langtag[1].toLowerCase();  // en-US => us
    }
    else if(langtag.length >= 3) {
        return langtag[1].toLowerCase();  // en-US => us
    }
}
function GetAlternateLanguage(){
    var userLanguage = window.navigator.userLanguage || window.navigator.language; // "en", "en-US", "fr", "fr-FR", "es-ES", etc.
    // langtag = language["-"script]["-" region] *("-" variant) *("-" extension) ["-" privateuse]
    if(Language != "auto") userLanguage = Language;
    userLanguage = userLanguage.toLowerCase();
    if(userLanguage == "en" || userLanguage.indexOf("en-") == 0) return "";  // English is already loaded

    for(l = 0; l < availableLang.length; l++){
        if(userLanguage.indexOf(availableLang[l].toLowerCase()) == 0){
            console.log("Alternate Language detected: ", userLanguage);
            // Set up Moment with the same language settings
            moment.locale(userLanguage);
            return availableLang[l].toLowerCase();
        }
    }
    return "";
}
function getFilter(filter, keyword){
    if(filter.indexOf(",", filter.indexOf(keyword +": ") + keyword.length + 2) != -1){
        return filter.substring(filter.indexOf(keyword +": ") + keyword.length + 2, filter.indexOf(",", filter.indexOf(keyword +": ") + keyword.length + 2));
    }
    else {
        return filter.substring(filter.indexOf(keyword +": ") + keyword.length + 2);
    }
}
function base64toBlob(base64Data, contentType) {
    if(base64Data.indexOf("," != -1)) base64Data = base64Data.split(",")[1]; // [data:image/png;base64] , [xxx...]
    var byteCharacters = atob(base64Data);
    var slicesCount = Math.ceil(byteCharacters.length / 1024);
    var byteArrays = new Array(slicesCount);
    for (var s = 0; s < slicesCount; ++s) {
        var begin = s * 1024;
        var end = Math.min(begin + 1024, byteCharacters.length);
        var bytes = new Array(end - begin);
        for (var offset = begin, i = 0; offset < end; ++i, ++offset) {
            bytes[i] = byteCharacters[offset].charCodeAt(0);
        }
        byteArrays[s] = new Uint8Array(bytes);
    }
    return new Blob(byteArrays, { type: contentType });
}
function MakeDataArray(defaultValue, count){
    var rtnArray = new Array(count);
    for(var i=0; i< rtnArray.length; i++) {
        rtnArray[i] = defaultValue;
    }
    return rtnArray;
}

// Window and Document Events
// ==========================
$(window).on("beforeunload", function(event) {
    endSession('2');
});
$(window).on("resize", function() {
    UpdateUI();
});
$(window).on("offline", function(){
    console.warn('Offline!');

    $("#regStatus").html(lang.disconnected_from_web_socket);
    $("#WebRtcFailed").show();

    // If there is an issue with the WS connection
    // We unregister, so that we register again once its up
    console.log("Disconnect Transport...");
    try{
        // userAgent.registerer.unregister();
        userAgent.transport.disconnect();
    } catch(e){
        // I know!!!
    }
});
$(window).on("online", function(){
    console.log('Online!');
    ReconnectTransport();
});
$(window).on("keypress", function(event) {
    // TODO: Add Shortcuts

    // console.log(event);
    if(event.ctrlKey){
        // You have the Ctrl Key pressed, this could be a Call Function
        // Blind Transfer the current Call
        if(event.key == "b"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Start Blind Transfer");
        }
        // Attended Transfer the current Call
        if(event.key == "a"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Start Attended Transfer");
        }
        // Audio Call current selected buddy
        if(event.key == "c"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Start Audio Call");
        }
        // Video Call current selected buddy
        if(event.key == "v"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Start Video Call");
        }
        // Hold (Toggle)
        if(event.key == "h"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Hold Toggle");
        }
        // Mute (Toggle)
        if(event.key == "m"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Mute Toggle");
        }
        // End current call
        if(event.key == "e"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: End current call");
        }
        // Recording (Start/Stop)
        if(event.key == "r"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Recording Toggle");
        }
        // Select line 1-9
        if(event.key == "1" || event.key == "2" | event.key == "3" || event.key == "4" || event.key == "5" || event.key == "6" || event.key == "7" || event.key == "8" || event.key == "9"){
            event.preventDefault();
            console.log("Keyboard Shortcut for: Select Line", event.key);
        }
    }
});
function SetUpCall(phone_number){

    navigator.permissions.query({name: 'microphone'}).then(function (PermissionStatus) {
        if (PermissionStatus.state == 'granted') {

            MakeMeThisCall(phone_number.toString());

        } else { // prompt OR denied
            checkPermissions(phone_number);
        }
    });

}

function checkPermissions(phone_number) {
    const permissions = navigator.mediaDevices.getUserMedia({audio: true, video: false})
    permissions.then((stream) => {
        SetUpCall(phone_number);
        $('#myModal').modal('hide');
    })
    .catch((err) => {
      this.setState(((prevState) => {
        havePermissions: false
      }));
      console.log(`${err.name} : ${err.message}`)
    });
  }

function MakeMeThisCall(NumberToBeCalled){
    // Load phoneOptions
    // =================
    // Note: These options can be defined in the containing HTML page, and simply defined as a global variable
    // var phoneOptions = {} // would work in index.html
    // Even if the setting is defined on the database, these variables get loaded after.

    var options = (typeof phoneOptions !== 'undefined')? phoneOptions : {};
    if(options.loadAlternateLang !== undefined) loadAlternateLang = options.loadAlternateLang;
    if(options.profileName !== undefined) profileName = options.profileName;
    if(options.imagesDirectory !== undefined) imagesDirectory = options.imagesDirectory;
    if(options.defaultAvatars !== undefined) defaultAvatars = options.defaultAvatars;
    if(options.wallpaperLight !== undefined) wallpaperLight = options.wallpaperLight;
    if(options.wallpaperDark !== undefined) wallpaperDark = options.wallpaperDark;
    if(options.wssServer !== undefined) wssServer = options.wssServer;
    if(options.WebSocketPort !== undefined) WebSocketPort = options.WebSocketPort;
    if(options.ServerPath !== undefined) ServerPath = options.ServerPath;
    if(options.SipDomain !== undefined) SipDomain = options.SipDomain;
    if(options.SipUsername !== undefined) SipUsername = options.SipUsername;
    if(options.SipPassword !== undefined) SipPassword = options.SipPassword;
    if(options.SingleInstance !== undefined) SingleInstance = options.SingleInstance;
    if(options.TransportConnectionTimeout !== undefined) TransportConnectionTimeout = options.TransportConnectionTimeout;
    if(options.TransportReconnectionAttempts !== undefined) TransportReconnectionAttempts = options.TransportReconnectionAttempts;
    if(options.TransportReconnectionTimeout !== undefined) TransportReconnectionTimeout = options.TransportReconnectionTimeout;
    if(options.SubscribeToYourself !== undefined) SubscribeToYourself = options.SubscribeToYourself;
    if(options.VoiceMailSubscribe !== undefined) VoiceMailSubscribe = options.VoiceMailSubscribe;
    if(options.VoicemailDid !== undefined) VoicemailDid = options.VoicemailDid;
    if(options.SubscribeVoicemailExpires !== undefined) SubscribeVoicemailExpires = options.SubscribeVoicemailExpires;
    if(options.ContactUserName !== undefined) ContactUserName = options.ContactUserName;
    if(options.userAgentStr !== undefined) userAgentStr = options.userAgentStr;
    if(options.hostingPrefix !== undefined) hostingPrefix = options.hostingPrefix;
    if(options.RegisterExpires !== undefined) RegisterExpires = options.RegisterExpires;
    if(options.RegisterExtraHeaders !== undefined) RegisterExtraHeaders = options.RegisterExtraHeaders;
    if(options.RegisterExtraContactParams !== undefined) RegisterExtraContactParams = options.RegisterExtraContactParams;
    if(options.RegisterContactParams !== undefined) RegisterContactParams = options.RegisterContactParams;
    if(options.WssInTransport !== undefined) WssInTransport = options.WssInTransport;
    if(options.IpInContact !== undefined) IpInContact = options.IpInContact;
    if(options.BundlePolicy !== undefined) BundlePolicy = options.BundlePolicy;
    if(options.IceStunServerJson !== undefined) IceStunServerJson = options.IceStunServerJson;
    if(options.IceStunCheckTimeout !== undefined) IceStunCheckTimeout = options.IceStunCheckTimeout;
    if(options.SubscribeBuddyAccept !== undefined) SubscribeBuddyAccept = options.SubscribeBuddyAccept;
    if(options.SubscribeBuddyEvent !== undefined) SubscribeBuddyEvent = options.SubscribeBuddyEvent;
    if(options.SubscribeBuddyExpires !== undefined) SubscribeBuddyExpires = options.SubscribeBuddyExpires;
    if(options.NoAnswerTimeout !== undefined) NoAnswerTimeout = options.NoAnswerTimeout;
    if(options.AutoAnswerEnabled !== undefined) AutoAnswerEnabled = options.AutoAnswerEnabled;
    if(options.DoNotDisturbEnabled !== undefined) DoNotDisturbEnabled = options.DoNotDisturbEnabled;
    if(options.CallWaitingEnabled !== undefined) CallWaitingEnabled = options.CallWaitingEnabled;
    if(options.RecordAllCalls !== undefined) RecordAllCalls = options.RecordAllCalls;
    if(options.StartVideoFullScreen !== undefined) StartVideoFullScreen = options.StartVideoFullScreen;
    if(options.SelectRingingLine !== undefined) SelectRingingLine = options.SelectRingingLine;
    if(options.UiMaxWidth !== undefined) UiMaxWidth = options.UiMaxWidth;
    if(options.UiThemeStyle !== undefined) UiThemeStyle = options.UiThemeStyle;
    if(options.UiMessageLayout !== undefined) UiMessageLayout = options.UiMessageLayout;
    if(options.UiCustomConfigMenu !== undefined) UiCustomConfigMenu = options.UiCustomConfigMenu;
    if(options.UiCustomDialButton !== undefined) UiCustomDialButton = options.UiCustomDialButton;
    if(options.UiCustomSortAndFilterButton !== undefined) UiCustomSortAndFilterButton = options.UiCustomSortAndFilterButton;
    if(options.UiCustomAddBuddy !== undefined) UiCustomAddBuddy = options.UiCustomAddBuddy;
    if(options.UiCustomEditBuddy !== undefined) UiCustomEditBuddy = options.UiCustomEditBuddy;
    if(options.UiCustomMediaSettings !== undefined) UiCustomMediaSettings = options.UiCustomMediaSettings;
    if(options.UiCustomMessageAction !== undefined) UiCustomMessageAction = options.UiCustomMessageAction;
    if(options.AutoGainControl !== undefined) AutoGainControl = options.AutoGainControl;
    if(options.EchoCancellation !== undefined) EchoCancellation = options.EchoCancellation;
    if(options.NoiseSuppression !== undefined) NoiseSuppression = options.NoiseSuppression;
    if(options.MirrorVideo !== undefined) MirrorVideo = options.MirrorVideo;
    if(options.maxFrameRate !== undefined) maxFrameRate = options.maxFrameRate;
    if(options.videoHeight !== undefined) videoHeight = options.videoHeight;
    if(options.MaxVideoBandwidth !== undefined) MaxVideoBandwidth = options.MaxVideoBandwidth;
    if(options.videoAspectRatio !== undefined) videoAspectRatio = options.videoAspectRatio;
    if(options.NotificationsActive !== undefined) NotificationsActive = options.NotificationsActive;
    if(options.StreamBuffer !== undefined) StreamBuffer = options.StreamBuffer;
    if(options.PosterJpegQuality !== undefined) PosterJpegQuality = options.PosterJpegQuality;
    if(options.VideoResampleSize !== undefined) VideoResampleSize = options.VideoResampleSize;
    if(options.RecordingVideoSize !== undefined) RecordingVideoSize = options.RecordingVideoSize;
    if(options.RecordingVideoFps !== undefined) RecordingVideoFps = options.RecordingVideoFps;
    if(options.RecordingLayout !== undefined) RecordingLayout = options.RecordingLayout;
    if(options.DidLength !== undefined) DidLength = options.DidLength;
    if(options.MaxDidLength !== undefined) MaxDidLength = options.MaxDidLength;
    if(options.DisplayDateFormat !== undefined) DisplayDateFormat = options.DisplayDateFormat;
    if(options.DisplayTimeFormat !== undefined) DisplayTimeFormat = options.DisplayTimeFormat;
    if(options.Language !== undefined) Language = options.Language;
    if(options.BuddySortBy !== undefined) BuddySortBy = options.BuddySortBy;
    if(options.SortByTypeOrder !== undefined) SortByTypeOrder = options.SortByTypeOrder;
    if(options.BuddyAutoDeleteAtEnd !== undefined) BuddyAutoDeleteAtEnd = options.BuddyAutoDeleteAtEnd;
    if(options.HideAutoDeleteBuddies !== undefined) HideAutoDeleteBuddies = options.HideAutoDeleteBuddies;
    if(options.BuddyShowExtenNum !== undefined) BuddyShowExtenNum = options.BuddyShowExtenNum;
    if(options.EnableTextMessaging !== undefined) EnableTextMessaging = options.EnableTextMessaging;
    if(options.DisableFreeDial !== undefined) DisableFreeDial = options.DisableFreeDial;
    if(options.DisableBuddies !== undefined) DisableBuddies = options.DisableBuddies;
    if(options.EnableTransfer !== undefined) EnableTransfer = options.EnableTransfer;
    if(options.EnableConference !== undefined) EnableConference = options.EnableConference;
    if(options.AutoAnswerPolicy !== undefined) AutoAnswerPolicy = options.AutoAnswerPolicy;
    if(options.DoNotDisturbPolicy !== undefined) DoNotDisturbPolicy = options.DoNotDisturbPolicy;
    if(options.CallWaitingPolicy !== undefined) CallWaitingPolicy = options.CallWaitingPolicy;
    if(options.CallRecordingPolicy !== undefined) CallRecordingPolicy = options.CallRecordingPolicy;
    if(options.IntercomPolicy !== undefined) IntercomPolicy = options.IntercomPolicy;
    if(options.EnableAccountSettings !== undefined) EnableAccountSettings = options.EnableAccountSettings;
    if(options.EnableAppearanceSettings !== undefined) EnableAppearanceSettings = options.EnableAppearanceSettings;
    if(options.EnableNotificationSettings !== undefined) EnableNotificationSettings = options.EnableNotificationSettings;
    if(options.EnableAlphanumericDial !== undefined) EnableAlphanumericDial = options.EnableAlphanumericDial;
    if(options.EnableVideoCalling !== undefined) EnableVideoCalling = options.EnableVideoCalling;
    if(options.EnableTextExpressions !== undefined) EnableTextExpressions = options.EnableTextExpressions;
    if(options.EnableTextDictate !== undefined) EnableTextDictate = options.EnableTextDictate;
    if(options.EnableRingtone !== undefined) EnableRingtone = options.EnableRingtone;
    if(options.MaxBuddies !== undefined) MaxBuddies = options.MaxBuddies;
    if(options.MaxBuddyAge !== undefined) MaxBuddyAge = options.MaxBuddyAge;
    if(options.ChatEngine !== undefined) ChatEngine = options.ChatEngine;
    if(options.XmppServer !== undefined) XmppServer = options.XmppServer;
    if(options.XmppWebsocketPort !== undefined) XmppWebsocketPort = options.XmppWebsocketPort;
    if(options.XmppWebsocketPath !== undefined) XmppWebsocketPath = options.XmppWebsocketPath;
    if(options.XmppDomain !== undefined) XmppDomain = options.XmppDomain;
    if(options.profileUser !== undefined) profileUser = options.profileUser;
    if(options.XmppRealm !== undefined) XmppRealm = options.XmppRealm;
    if(options.XmppRealmSeparator !== undefined) XmppRealmSeparator = options.XmppRealmSeparator;
    if(options.XmppChatGroupService !== undefined) XmppChatGroupService = options.XmppChatGroupService;
    lang = {"create_group":"Create Group","add_someone":"Add Someone","find_someone":"Find someone...","refresh_registration":"Refresh Registration","configure_extension":"Configure Extension","auto_answer":"Auto Answer","do_no_disturb":"Do Not Disturb","call_waiting":"Call Waiting","record_all_calls":"Record All Calls","extension_number":"Extension Number","email":"Email","mobile":"Mobile","alternative_contact":"Alternate Contact","full_name":"Full Name","eg_full_name":"eg: Keyla James","title_description":"Title / Description","eg_general_manager":"eg: General Manager","internal_subscribe_extension":"Subscribe Extension (Internal)","eg_internal_subscribe_extension":"eg: 100 or john","mobile_number":"Mobile Number","eg_mobile_number":"eg: +44 123-456 7890","eg_email":"eg: Keyla.James@innovateasterisk.com","contact_number_1":"Contact Number 1","eg_contact_number_1":"eg: +1 234 567 8901","contact_number_2":"Contact Number 2","eg_contact_number_2":"eg: +441234567890","add":"Add","cancel":"Cancel","save":"Save","reload_required":"Reload Required","alert_settings":"In order to apply these settings, the page must reload, OK?","account":"Account","audio_video":"Audio & Video","appearance":"Appearance","notifications":"Notifications","asterisk_server_address":"Secure WebSocket Server (TLS)","eg_asterisk_server_address":"eg: ws.innovateasterisk.com","websocket_port":"WebSocket Port","eg_websocket_port":"eg: 4443","websocket_path":"WebSocket Path","eg_websocket_path":"/ws","sip_domain":"Domain","eg_sip_domain":"eg: innovateasterisk.com","sip_username":"SIP Username","eg_sip_username":"eg: webrtc","sip_password":"SIP Password","eg_sip_password":"eg: 1234","speaker":"Speaker","microphone":"Microphone","camera":"Camera","frame_rate":"Frame Rate (per second)","quality":"Quality","image_orientation":"Image Orientation","image_orientation_normal":"Normal","image_orientation_mirror":"Mirror","aspect_ratio":"Aspect Ratio","preview":"Preview","ringtone":"Ringtone","ring_device":"Ring Device","auto_gain_control":"Auto Gain Control","echo_cancellation":"Echo Cancellation","noise_suppression":"Noise Suppression","enable_onscreen_notifications":"Enabled Onscreen Notifications","alert_notification_permission":"You need to accept the permission request to allow Notifications","permission":"Permission","error":"Error","alert_media_devices":"MediaDevices was null -  Check if your connection is secure (HTTPS)","alert_error_user_media":"Error getting User Media.","alert_file_size":"The file is bigger than 50MB, you cannot upload this file","alert_single_file":"Select a single file","alert_not_found":"This item was not found","edit":"Edit","welcome":"Welcome","accept":"Accept","registered":"Registered","registration_failed":"Registration Failed","unregistered":"Unregistered, bye!","connected_to_web_socket":"Connected to Web Socket!","disconnected_from_web_socket":"Disconnected from Web Socket!","web_socket_error":"Web Socket Error","connecting_to_web_socket":"Connecting to Web Socket...","error_connecting_web_socket":"Error connecting to the server on the WebSocket port","sending_registration":"Sending Registration...","unsubscribing":"Unsubscribing...","disconnecting":"Disconnecting...","incoming_call":"Incoming Call","incoming_call_from":"Incoming call from:","answer_call":"Answer Call","answer_call_with_video":"Answer Call with Video","reject_call":"Reject Call","call_failed":"Call Failed","alert_no_microphone":"Sorry, you don't have any Microphone connected to this computer. You cannot receive calls.","call_in_progress":"Call in Progress!","call_rejected":"Call Rejected","trying":"Trying...","ringing":"Ringing...","call_cancelled":"Call Cancelled","call_ended":"Call ended, bye!","yes":"Yes","no":"No","receive_kilobits_per_second":"Receive Kilobits per second","receive_packets_per_second":"Receive Packets per second","receive_packet_loss":"Receive Packet Loss","receive_jitter":"Receive Jitter","receive_audio_levels":"Receive Audio Levels","send_kilobits_per_second":"Send Kilobits Per Second","send_packets_per_second":"Send Packets Per Second","state_not_online":"Not online","state_ready":"Ready","state_on_the_phone":"On the phone","state_ringing":"Ringing","state_on_hold":"On hold","state_unavailable":"Unavailable","state_unknown":"Unknown","alert_empty_text_message":"Please enter something into the text box provided and click send","no_message":"No Message","message_from":"Message from","starting_video_call":"Starting Video Call...","call_extension":"Call Extension","call_mobile":"Call Mobile","call_number":"Call Number","call_group":"Call Group","starting_audio_call":"Starting Audio Call...","call_recording_started":"Call Recording Started","call_recording_stopped":"Call Recording Stopped","confirm_stop_recording":"Are you sure you want to stop recording this call?","stop_recording":"Stop Recording?","width":"Width","height":"Height","extension":"Extension","call_blind_transfered":"Call Blind Transferred","connecting":"Connecting...","attended_transfer_call_started":"Attended Transfer Call Started...","attended_transfer_call_cancelled":"Attended Transfer Call Cancelled","attended_transfer_complete_accepted":"Attended Transfer Complete (Accepted)","attended_transfer_complete":"Attended Transfer complete","attended_transfer_call_ended":"Attended Transfer Call Ended","attended_transfer_call_rejected":"Attended Transfer Call Rejected","attended_transfer_call_terminated":"Attended Transfer Call Terminated","conference_call_started":"Conference Call Started...","conference_call_cancelled":"Conference Call Cancelled","conference_call_in_progress":"Conference Call In Progress","conference_call_ended":"Conference Call Ended","conference_call_rejected":"Conference Call Rejected","conference_call_terminated":"Conference Call Terminated","null_session":"Session Error, Null","call_on_hold":"Call on Hold","send_dtmf":"Sent DTMF","switching_video_source":"Switching video source","switching_to_canvas":"Switching to canvas","switching_to_shared_video":"Switching to Shared Video","switching_to_shared_screen":"Switching to Shared Screen","video_disabled":"Video Disabled","line":"Line","back":"Back","audio_call":"Audio Call","video_call":"Video Call","find_something":"Find Something","remove":"Remove","present":"Present","scratchpad":"Scratchpad","screen":"Screen","video":"Video","blank":"Blank","show_key_pad":"Show Key Pad","mute":"Mute","unmute":"Unmute","start_call_recording":"Start Call Recording","stop_call_recording":"Stop Call Recording","transfer_call":"Transfer Call","cancel_transfer":"Cancel Transfer","conference_call":"Conference Call","cancel_conference":"Cancel Conference","hold_call":"Hold Call","resume_call":"Resume Call","end_call":"End Call","search_or_enter_number":"Search or enter number","blind_transfer":"Blind Transfer","attended_transfer":"Attended Transfer","complete_transfer":"Complete Transfer","end_transfer_call":"End Transfer Call","call":"Call","cancel_call":"Cancel Call","join_conference_call":"Join Conference Call","end_conference_call":"End Conference Call","microphone_levels":"Microphone Levels","speaker_levels":"Speaker Levels","send_statistics":"Send Statistics","receive_statistics":"Receive Statistics","find_something_in_the_message_stream":"Find something in the message stream...","type_your_message_here":"Type your message here...","menu":"Menu","confirm_remove_buddy":"This buddy will be removed from your list. Confirm remove?","remove_buddy":"Remove Buddy","read_more":"Read More","started":"Started","stopped":"Stopped","recording_duration":"Recording Duration","a_video_call":"a video call","an_audio_call":"an audio call","you_tried_to_make":"You tried to make","you_made":"You made","and_spoke_for":"and spoke for","you_missed_a_call":"You missed a call","you_received":"You received","second_single":"second","seconds_plural":"seconds","minute_single":"minute","minutes_plural":"minutes","hour_single":"hour","hours_plural":"hours","bytes":"Bytes","kb":"KB","mb":"MB","gb":"GB","tb":"TB","pb":"PB","eb":"EB","zb":"ZB","yb":"YB","call_on_mute":"Call on Mute","call_off_mute":"Call off Mute","tag_call":"Tag Call","clear_flag":"Clear Flag","flag_call":"Flag Call","edit_comment":"Edit Comment","copy_message":"Copy Message","quote_message":"Quote Message","select_expression":"Select Expression","dictate_message":"Dictate Message","alert_speech_recognition":"Your browser does not support this function, sorry","speech_recognition":"Speech Recognition","im_listening":"I'm listening...","msg_silence_detection":"You were quiet for a while so voice recognition turned itself off.","msg_no_speech":"No speech was detected. Try again.","loading":"Loading...","select_video":"Select Video","ok":"OK","device_settings":"Device Settings","call_stats":"Call Stats","you_received_a_call_from":"You received a call from","you_made_a_call_to":"You made a call to","you_answered_after":"You answered after","they_answered_after":"They answered after","with_video":"with video","you_started_a_blind_transfer_to":"You started a blind transfer to","you_started_an_attended_transfer_to":"You started an attended transfer to","the_call_was_completed":"The call was completed.","the_call_was_not_completed":"The call was not completed.","you_put_the_call_on_mute":"You put the call on mute.","you_took_the_call_off_mute":"You took the call off mute.","you_put_the_call_on_hold":"You put the call on hold.","you_took_the_call_off_hold":"You took the call off hold.","you_ended_the_call":"You ended the call.","they_ended_the_call":"They ended the call.","call_is_being_recorded":"Call is being recorded.","now_stopped":"Now Stopped","you_started_a_conference_call_to":"You started a conference call to","show_call_detail_record":"Show Call Detail Record","call_detail_record":"Call Detail Record","call_direction":"Call Direction","call_date_and_time":"Call Date & Time","ring_time":"Ring Time","talk_time":"Talk Time","call_duration":"Call Duration","flagged":"Flagged","call_tags":"Call Tags","call_notes":"Call Notes","activity_timeline":"Activity Timeline","call_recordings":"Call Recordings","save_as":"Save As","right_click_and_select_save_link_as":"Right click and select Save Link As","send":"Send","set_status":"Set Status","default_status":"(No Status)","is_typing":"is typing","chat_engine":"Chat Engine","xmpp_server_address":"Secure XMPP Server (TLS)","eg_xmpp_server_address":"eg: xmpp.innovateasterisk.com","allow_calls_on_dnd":"Allow calls during Do Not Disturb","basic_extension":"Basic Extension","extension_including_xmpp":"Extension including Message Exchange","addressbook_contact":"Address Book Contact","subscribe_to_dev_state":"Subscribe to Device State Notifications","default_video_src":"Default","subscribe_voicemail":"Subscribe to VoiceMail (MWI)","voicemail_did":"VoiceMail Management Number","filter_and_sort":"Filter and Sort","sort_type":"Type (then Last Activity)","sort_type_cex":"Contacts, SIP then XMPP","sort_type_cxe":"Contacts, XMPP then SIP","sort_type_xec":"XMPP, SIP then Contacts","sort_type_xce":"XMPP, Contacts then SIP","sort_type_exc":"SIP, XMPP then Contacts","sort_type_ecx":"SIP, Contacts then XMPP","sort_exten":"Extension or Number (then Last Activity)","sort_alpha":"Alphabetically (then Last Activity)","sort_activity":"Only Last Activity","sort_auto_delete_at_end":"Show Auto Delete at the end","sort_auto_delete_hide":"Hide Auto Delete Buddies","sort_show_exten_num":"Show Extension Numbers","sort_no_showing":"Not showing {0} Auto Delete buddies","delete_buddy":"Delete","delete_duddy_data":"Delete History","pin_to_top":"Pinned","voice_mail":"VoiceMail","you_have_new_voice_mail":"You have {0} new VoiceMail messages.","new_voice_mail":"New VoiceMail Message"};

    console.log("Runtime options", options);

    // Single Instance Check
    if(SingleInstance == true){
        console.log("Instance ID :", instanceID);
        // First we set (or try to set) the instance ID
        localDB.setItem("InstanceId", instanceID);

        // Now we attach a listener
        window.addEventListener('storage', onLocalStorageEvent, false);
    }

    InitUi(NumberToBeCalled);

}

function onLocalStorageEvent(event){
    if(event.key == "InstanceId"){
        // Another script is writing to the local storage,
        // because the event lister is attached after the
        // Instance ID, its from another window/tab/script.

        // Because you cannot change focus to another tab (even
        // from a tab with the same domain), and because you cannot
        // close a tab, the best we can do is de-register this
        // UserAgent, so that we are only registered here.

        Unregister();
        // TOO: what if you re-register?
        // Should this unload the entire page, what about calls?
    }
}


// User Interface
// ==============
function UpdateUI(){
    var windowWidth = $(window).outerWidth()
    var windowHeight = $(window).outerHeight();
    if(windowWidth > UiMaxWidth){
        $("#leftContentTable").css("border-left-width", "1px");
        if(selectedBuddy == null && selectedLine == null) {
            $("#leftContentTable").css("border-right-width", "1px");
        } else {
            $("#rightContent").css("border-right-width", "1px");
        }
    } else {
        // Touching Edges
        $("#leftContentTable").css("border-left-width", "0px");
        if(selectedBuddy == null && selectedLine == null) {
            $("#leftContentTable").css("border-right-width", "0px");
        } else {
            $("#leftContentTable").css("border-right-width", "1px");
        }
        $("#rightContent").css("border-right-width", "0px");
    }

    if(windowWidth < 920){
        // Narrow Layout

        if(selectedBuddy == null & selectedLine == null) {
            // Nobody Selected (SHow Only Left Table)
            $("#rightContent").hide();

            $("#leftContent").css("width", "100%");
            $("#leftContent").show();
        }
        else {
            // Nobody Selected (SHow Only Buddy / Line)
            $("#rightContent").css("margin-left", "0px");
            $("#rightContent").show();

            $("#leftContent").hide();

            if(selectedBuddy != null) updateScroll(selectedBuddy.identity);
        }
    }
    else {
        // Wide Screen Layout
        if(selectedBuddy == null & selectedLine == null) {
            $("#leftContent").css("width", "100%");
            $("#rightContent").css("margin-left", "0px");
            $("#leftContent").show();
            $("#rightContent").hide();
        }
        else{
            $("#leftContent").css("width", "320px");
            $("#rightContent").css("margin-left", "320px");
            $("#leftContent").show();
            $("#rightContent").show();

            if(selectedBuddy != null) updateScroll(selectedBuddy.identity);
        }
    }
    for(var l=0; l<Lines.length; l++){
        updateLineScroll(Lines[l].LineNumber);
        RedrawStage(Lines[l].LineNumber, false);
    }

    if(windowObj != null){
        var offsetTextHeight = windowObj.parent().outerHeight();
        var width = windowObj.width();
        if(windowWidth <= width || windowHeight <= offsetTextHeight) {
            // First apply to dialog, then set css
            windowObj.dialog("option", "height", windowHeight);
            windowObj.dialog("option", "width", windowWidth - (1+1+2+2)); // There is padding and a border
            windowObj.parent().css('top', '0px');
            windowObj.parent().css('left', '0px');
        }
        else {
            windowObj.parent().css('left', windowWidth/2 - width/2 + 'px');
            windowObj.parent().css('top', windowHeight/2 - offsetTextHeight/2 + 'px');
        }
    }
    if(alertObj != null){
        var width = 300;
        var offsetTextHeight = alertObj.parent().outerHeight();
        if(windowWidth <= width || windowHeight <= offsetTextHeight) {
            if(windowWidth <= width){
                // First apply to dialog, then set css
                alertObj.dialog("option", "width", windowWidth - (1+1+2+2));
                alertObj.parent().css('left', '0px');
                alertObj.parent().css('top', windowHeight/2 - offsetTextHeight/2 + 'px');
            }
            if(windowHeight <= offsetTextHeight){
                // First apply to dialog, then set css
                alertObj.dialog("option", "height", windowHeight);
                alertObj.parent().css('left', windowWidth/2 - width/2 + 'px');
                alertObj.parent().css('top', '0px');
            }
        }
        else {
            alertObj.parent().css('left', windowWidth/2 - width/2 + 'px');
            alertObj.parent().css('top', windowHeight/2 - offsetTextHeight/2 + 'px');
        }
    }
    if(confirmObj != null){
        var width = 300;
        var offsetTextHeight = confirmObj.parent().outerHeight();
        if(windowWidth <= width || windowHeight <= offsetTextHeight) {
            if(windowWidth <= width){
                // First apply to dialog, then set css
                confirmObj.dialog("option", "width", windowWidth - (1+1+2+2));
                confirmObj.parent().css('left', '0px');
                confirmObj.parent().css('top', windowHeight/2 - offsetTextHeight/2 + 'px');
            }
            if(windowHeight <= offsetTextHeight){
                // First apply to dialog, then set css
                confirmObj.dialog("option", "height", windowHeight);
                confirmObj.parent().css('left', windowWidth/2 - width/2 + 'px');
                confirmObj.parent().css('top', '0px');
            }
        }
        else {
            confirmObj.parent().css('left', windowWidth/2 - width/2 + 'px');
            confirmObj.parent().css('top', windowHeight/2 - offsetTextHeight/2 + 'px');
        }
    }
    if(promptObj != null){
        var width = 300;
        var offsetTextHeight = promptObj.parent().outerHeight();
        if(windowWidth <= width || windowHeight <= offsetTextHeight) {
            if(windowWidth <= width){
                // First apply to dialog, then set css
                promptObj.dialog("option", "width", windowWidth - (1+1+2+2));
                promptObj.parent().css('left', '0px');
                promptObj.parent().css('top', windowHeight/2 - offsetTextHeight/2 + 'px');
            }
            if(windowHeight <= offsetTextHeight){
                // First apply to dialog, then set css
                promptObj.dialog("option", "height", windowHeight);
                promptObj.parent().css('left', windowWidth/2 - width/2 + 'px');
                promptObj.parent().css('top', '0px');
            }
        }
        else {
            promptObj.parent().css('left', windowWidth/2 - width/2 + 'px');
            promptObj.parent().css('top', windowHeight/2 - offsetTextHeight/2 + 'px');
        }
    }
    HidePopup();
}

// UI Windows
// ==========
function AddSomeoneWindow(numberStr){
    CloseUpSettings();

    $("#myContacts").hide();
    $("#searchArea").hide();
    $("#actionArea").empty();

    var html = "<div style=\"text-align:right\"><button class=roundButtons onclick=\"ShowContacts()\"><i class=\"fa fa-close\"></i></button></div>"

    html += "<div border=0 class=UiSideField>";

    html += "<div class=UiText>"+ lang.full_name +":</div>";
    html += "<div><input id=AddSomeone_Name class=UiInputText type=text placeholder='"+ lang.eg_full_name +"'></div>";
    html += "<div><input type=checkbox id=AddSomeone_Dnd><label for=AddSomeone_Dnd>"+ lang.allow_calls_on_dnd +"</label></div>";

    // Type
    html += "<ul style=\"list-style-type:none\">";
    html += "<li><input type=radio name=buddyType id=type_exten checked><label for=type_exten>"+ lang.basic_extension +"</label>";
    if(ChatEngine == "XMPP"){
        html += "<li><input type=radio name=buddyType id=type_xmpp><label for=type_xmpp>"+ lang.extension_including_xmpp +"</label>";
    }
    html += "<li><input type=radio name=buddyType id=type_contact><label for=type_contact>"+ lang.addressbook_contact +"</label>";
    html += "</ul>";

    html += "<div id=RowDescription>";
    html += "<div class=UiText>"+ lang.title_description +":</div>";
    html += "<div><input id=AddSomeone_Desc class=UiInputText type=text placeholder='"+ lang.eg_general_manager +"'></div>";
    html += "</div>";

    html += "<div id=RowExtension>";
    html += "<div class=UiText>"+ lang.extension_number +":</div>";
    html += "<div><input id=AddSomeone_Exten class=UiInputText type=text placeholder='"+ lang.eg_internal_subscribe_extension +"'></div>";
    html += "<div><input type=checkbox id=AddSomeone_Subscribe><label for=AddSomeone_Subscribe>"+ lang.subscribe_to_dev_state +"</label></div>";
    html += "<div id=RowSubscribe style=\"display:none; margin-left:30px;\">";
    html += "<div class=UiText>"+ lang.internal_subscribe_extension +":</div>";
    html += "<div><input id=AddSomeone_SubscribeUser class=UiInputText type=text placeholder='"+ lang.eg_internal_subscribe_extension +"'></div>";
    html += "</div>";
    html += "</div>";

    html += "<div id=RowMobileNumber>";
    html += "<div class=UiText>"+ lang.mobile_number +":</div>";
    html += "<div><input id=AddSomeone_Mobile class=UiInputText type=tel placeholder='"+ lang.eg_mobile_number +"'></div>";
    html += "</div>";

    html += "<div id=RowEmail>";
    html += "<div class=UiText>"+ lang.email +":</div>";
    html += "<div><input id=AddSomeone_Email class=UiInputText type=email placeholder='"+ lang.eg_email +"'></div>";
    html += "</div>";

    html += "<div id=RowContact1>";
    html += "<div class=UiText>"+ lang.contact_number_1 +":</div>";
    html += "<div><input id=AddSomeone_Num1 class=UiInputText type=text placeholder='"+ lang.eg_contact_number_1 +"'></div>";
    html += "</div>";

    html += "<div id=RowContact2>";
    html += "<div class=UiText>"+ lang.contact_number_2 +":</div>";
    html += "<div><input id=AddSomeone_Num2 class=UiInputText type=text placeholder='"+ lang.eg_contact_number_2 +"'></div>";
    html += "</div>";

    html += "<div id=Persistance>";
    html += "<div class=UiText>Auto Delete:</div>";
    html += "<div><input type=checkbox id=AddSomeone_AutoDelete><label for=AddSomeone_AutoDelete>"+ lang.yes +"</label></div>";
    html += "</div>";

    html += "</div>";

    html += "<div class=UiWindowButtonBar id=ButtonBar></div>";

    $("#actionArea").html(html);

    // Button Actions
    var buttons = [];
    buttons.push({
        text: lang.add,
        action: function(){
            // Basic Validation
            if($("#AddSomeone_Name").val() == "") return;
            if(type == "extension" || type == "xmpp"){
                if($("#AddSomeone_Exten").val() == "") return;
            }

            var type = "extension";
            if($("#type_exten").is(':checked')){
                type = "extension";
                if($("#AddSomeone_Subscribe").is(':checked') && $("#AddSomeone_SubscribeUser").val() == ""){
                    $("#AddSomeone_SubscribeUser").val($("#AddSomeone_Exten").val())
                }
            } else if($("#type_xmpp").is(':checked')){
                type = "xmpp";
                if($("#AddSomeone_Subscribe").is(':checked') && $("#AddSomeone_SubscribeUser").val() == ""){
                    $("#AddSomeone_SubscribeUser").val($("#AddSomeone_Exten").val())
                }
            } else if($("#type_contact").is(':checked')){
                type = "contact";
            }

            // Add Contact / Extension
            var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
            if(json == null) json = InitUserBuddies();

            var buddyObj = null;
            if(type == "extension"){
                // Add Extension
                var id = uID();
                var dateNow = utcDateNow();
                json.DataCollection.push(
                    {
                        Type: "extension",
                        LastActivity: dateNow,
                        ExtensionNumber: $("#AddSomeone_Exten").val(),
                        MobileNumber: $("#AddSomeone_Mobile").val(),
                        ContactNumber1: $("#AddSomeone_Num1").val(),
                        ContactNumber2: $("#AddSomeone_Num2").val(),
                        uID: id,
                        cID: null,
                        gID: null,
                        jid: null,
                        DisplayName: $("#AddSomeone_Name").val(),
                        Description: $("#AddSomeone_Desc").val(),
                        Email: $("#AddSomeone_Email").val(),
                        MemberCount: 0,
                        EnableDuringDnd: $("#AddSomeone_Dnd").is(':checked'),
                        Subscribe: $("#AddSomeone_Subscribe").is(':checked'),
                        SubscribeUser: $("#AddSomeone_SubscribeUser").val(),
                        AutoDelete: $("#AddSomeone_AutoDelete").is(':checked')
                    }
                );
                buddyObj = new Buddy("extension",
                                        id,
                                        $("#AddSomeone_Name").val(),
                                        $("#AddSomeone_Exten").val(),
                                        $("#AddSomeone_Mobile").val(),
                                        $("#AddSomeone_Num1").val(),
                                        $("#AddSomeone_Num2").val(),
                                        dateNow,
                                        $("#AddSomeone_Desc").val(),
                                        $("#AddSomeone_Email").val(),
                                        jid,
                                        $("#AddSomeone_Dnd").is(':checked'),
                                        $("#AddSomeone_Subscribe").is(':checked'),
                                        $("#AddSomeone_SubscribeUser").val(),
                                        $("#AddSomeone_AutoDelete").is(':checked'));

                // Add memory object
                AddBuddy(buddyObj, false, false, $("#AddSomeone_Subscribe").is(':checked'), true);
            }
            if(type == "xmpp"){
                // Add XMPP extension
                var id = uID();
                var dateNow = utcDateNow();
                var jid = $("#AddSomeone_Exten").val() +"@"+ SipDomain;
                if(XmppRealm != "" && XmppRealmSeparator != "") jid = XmppRealm +""+ XmppRealmSeparator +""+ jid;
                json.DataCollection.push(
                    {
                        Type: "xmpp",
                        LastActivity: dateNow,
                        ExtensionNumber: $("#AddSomeone_Exten").val(),
                        MobileNumber: null,
                        ContactNumber1: null,
                        ContactNumber2: null,
                        uID: id,
                        cID: null,
                        gID: null,
                        jid: jid,
                        DisplayName: $("#AddSomeone_Name").val(),
                        Description: null,
                        Email: null,
                        MemberCount: 0,
                        EnableDuringDnd: $("#AddSomeone_Dnd").is(':checked'),
                        Subscribe: $("#AddSomeone_Subscribe").is(':checked'),
                        SubscribeUser: $("#AddSomeone_SubscribeUser").val(),
                        AutoDelete: $("#AddSomeone_AutoDelete").is(':checked')
                    }
                );
                buddyObj = new Buddy("xmpp",
                                        id,
                                        $("#AddSomeone_Name").val(),
                                        $("#AddSomeone_Exten").val(),
                                        "",
                                        "",
                                        "",
                                        dateNow,
                                        "",
                                        "",
                                        jid,
                                        $("#AddSomeone_Dnd").is(':checked'),
                                        $("#AddSomeone_Subscribe").is(':checked'),
                                        $("#AddSomeone_SubscribeUser").val(),
                                        $("#AddSomeone_AutoDelete").is(':checked'));

                // XMPP add to roster
                XmppAddBuddyToRoster(buddyObj);

                // Add memory object
                AddBuddy(buddyObj, false, false, $("#AddSomeone_Subscribe").is(':checked'), true);
            }
            if(type == "contact"){
                // Add Regular Contact
                var id = uID();
                var dateNow = utcDateNow();
                json.DataCollection.push(
                    {
                        Type: "contact",
                        LastActivity: dateNow,
                        ExtensionNumber: "",
                        MobileNumber: $("#AddSomeone_Mobile").val(),
                        ContactNumber1: $("#AddSomeone_Num1").val(),
                        ContactNumber2: $("#AddSomeone_Num2").val(),
                        uID: null,
                        cID: id,
                        gID: null,
                        jid: null,
                        DisplayName: $("#AddSomeone_Name").val(),
                        Description: $("#AddSomeone_Desc").val(),
                        Email: $("#AddSomeone_Email").val(),
                        MemberCount: 0,
                        EnableDuringDnd: $("#AddSomeone_Dnd").is(':checked'),
                        Subscribe: false,
                        SubscribeUser: null,
                        AutoDelete: $("#AddSomeone_AutoDelete").is(':checked')
                    }
                );
                buddyObj = new Buddy("contact",
                                        id,
                                        $("#AddSomeone_Name").val(),
                                        "",
                                        $("#AddSomeone_Mobile").val(),
                                        $("#AddSomeone_Num1").val(),
                                        $("#AddSomeone_Num2").val(),
                                        dateNow,
                                        $("#AddSomeone_Desc").val(),
                                        $("#AddSomeone_Email").val(),
                                        jid,
                                        $("#AddSomeone_Dnd").is(':checked'),
                                        false,
                                        null,
                                        $("#AddSomeone_AutoDelete").is(':checked'));

                // Add memory object
                AddBuddy(buddyObj, false, false, false, true);
            }

            // Save To DB
            json.TotalRows = json.DataCollection.length;
            localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));

            UpdateBuddyList();

            ShowContacts();
        }
    });
    buttons.push({
        text: lang.cancel,
        action: function(){
            ShowContacts();
        }
    });
    $.each(buttons, function(i,obj){
        var button = $('<button>'+ obj.text +'</button>').click(obj.action);
        $("#ButtonBar").append(button);
    });

    // Show
    $("#actionArea").show();
    $("#AddSomeone_Name").focus();

    // Do Onload
    window.setTimeout(function(){
        $("#type_exten").change(function(){
            if($("#type_exten").is(':checked')){
                $("#RowDescription").show();
                $("#RowExtension").show();
                $("#RowMobileNumber").show();
                $("#RowEmail").show();
                $("#RowContact1").show();
                $("#RowContact2").show();
            }
        });
        $("#type_xmpp").change(function(){
            if($("#type_xmpp").is(':checked')){
                $("#RowDescription").hide();
                $("#RowExtension").show();
                $("#RowMobileNumber").hide();
                $("#RowEmail").hide();
                $("#RowContact1").hide();
                $("#RowContact2").hide();
            }
        });
        $("#type_contact").change(function(){
            if($("#type_contact").is(':checked')){
                $("#RowDescription").show();
                $("#RowExtension").hide();
                $("#RowMobileNumber").show();
                $("#RowEmail").show();
                $("#RowContact1").show();
                $("#RowContact2").show();
            }
        });
        $("#AddSomeone_Subscribe").change(function(){
            if($("#AddSomeone_Subscribe").is(':checked')){
                if($("#AddSomeone_Exten").val() != "" && $("#AddSomeone_SubscribeUser").val() == ""){
                    $("#AddSomeone_SubscribeUser").val($("#AddSomeone_Exten").val());
                }
                $("#RowSubscribe").show();
            } else {
                $("#RowSubscribe").hide();
            }
        });
    }, 0);
}
function CreateGroupWindow(){
    // lang.create_group
}
function checkNotificationPromise() {
    try {
        Notification.requestPermission().then();
    }
    catch(e) {
        return false;
    }
    return true;
}
function HandleNotifyPermission(p){
    if(p == "granted") {
        // Good
    }
    else {
        Alert(lang.alert_notification_permission, lang.permission, function(){
            console.log("Attempting to uncheck the checkbox...");
            $("#Settings_Notifications").prop("checked", false);
        });
    }
}
function EditBuddyWindow(buddy){

    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null){
        Alert(lang.alert_not_found, lang.error);
        return;
    }
    var buddyJson = {};
    var itemId = -1;
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    $.each(json.DataCollection, function (i, item) {
        if(item.uID == buddy || item.cID == buddy || item.gID == buddy){
            buddyJson = item;
            itemId = i;
            return false;
        }
    });

    if(buddyJson == {}){
        Alert(lang.alert_not_found, lang.error);
        return;
    }
    if(UiCustomEditBuddy == true){
        if(typeof web_hook_on_edit_buddy !== 'undefined') {
            web_hook_on_edit_buddy(buddyJson);
        }
        return;
    }

    var cropper;

    var html = "<div border=0 class='UiWindowField'>";

    html += "<div id=ImageCanvas style=\"width:150px; height:150px\"></div>";
    html += "<div style=\"float:left; margin-left:200px;\"><input id=fileUploader type=file></div>";
    html += "<div style=\"margin-top: 50px\"></div>";

    html += "<div class=UiText>"+ lang.full_name +":</div>";
    html += "<div><input id=AddSomeone_Name class=UiInputText type=text placeholder='"+ lang.eg_full_name +"' value='"+ ((buddyJson.DisplayName && buddyJson.DisplayName != "null" && buddyJson.DisplayName != "undefined")? buddyJson.DisplayName : "") +"'></div>";
    html += "<div><input type=checkbox id=AddSomeone_Dnd "+ ((buddyJson.EnableDuringDnd == true)? "checked" : "" ) +"><label for=AddSomeone_Dnd>Allow calls while on Do Not Disturb</label></div>";

    html += "<div class=UiText>"+ lang.title_description +":</div>";
    html += "<div><input id=AddSomeone_Desc class=UiInputText type=text placeholder='"+ lang.eg_general_manager +"' value='"+ ((buddyJson.Description && buddyJson.Description != "null" && buddyJson.Description != "undefined")? buddyJson.Description : "") +"'></div>";

    if(buddyJson.Type == "extension" || buddyJson.Type == "xmpp"){
        html += "<div class=UiText>"+ lang.extension_number +": </div>";
        html += "<div><input id=AddSomeone_Exten class=UiInputText type=text value="+ buddyJson.ExtensionNumber +"></div>";
        html += "<div><input type=checkbox id=AddSomeone_Subscribe "+ ((buddyJson.Subscribe == true)? "checked" : "" ) +"><label for=AddSomeone_Subscribe>Subscribe to Device State Notifications</label></div>";
        html += "<div id=RowSubscribe style=\"display:"+ ((buddyJson.Subscribe == true)? "unset" : "none" ) +";\">";
        html += "<div class=UiText style=\"margin-left:30px\">"+ lang.internal_subscribe_extension +":</div>";
        html += "<div style=\"margin-left:30px\"><input id=AddSomeone_SubscribeUser class=UiInputText type=text placeholder='"+ lang.eg_internal_subscribe_extension +"' value='"+ ((buddyJson.SubscribeUser && buddyJson.SubscribeUser != "null" && buddyJson.SubscribeUser != "undefined")? buddyJson.SubscribeUser : "") +"'></div>";
        html += "</div>";
    }
    else {
        html += "<input type=checkbox id=AddSomeone_Subscribe style=\"display:none\">";
    }
    html += "<div class=UiText>"+ lang.mobile_number +":</div>";
    html += "<div><input id=AddSomeone_Mobile class=UiInputText type=text placeholder='"+ lang.eg_mobile_number +"' value='"+ ((buddyJson.MobileNumber && buddyJson.MobileNumber != "null" && buddyJson.MobileNumber != "undefined")? buddyJson.MobileNumber : "") +"'></div>";

    html += "<div class=UiText>"+ lang.email +":</div>";
    html += "<div><input id=AddSomeone_Email class=UiInputText type=text placeholder='"+ lang.eg_email +"' value='"+ ((buddyJson.Email && buddyJson.Email != "null" && buddyJson.Email != "undefined")? buddyJson.Email : "") +"'></div>";

    html += "<div class=UiText>"+ lang.contact_number_1 +":</div>";
    html += "<div><input id=AddSomeone_Num1 class=UiInputText type=text placeholder='"+ lang.eg_contact_number_1 +"' value='"+((buddyJson.ContactNumber1 && buddyJson.ContactNumber1 != "null" && buddyJson.ContactNumber1 != "undefined")? buddyJson.ContactNumber1 : "") +"'></div>";

    html += "<div class=UiText>"+ lang.contact_number_2 +":</div>";
    html += "<div><input id=AddSomeone_Num2 class=UiInputText type=text placeholder='"+ lang.eg_contact_number_2 +"' value='"+ ((buddyJson.ContactNumber2 && buddyJson.ContactNumber2 != "null" && buddyJson.ContactNumber2 != "undefined")? buddyJson.ContactNumber2 : "") +"'></div>";

    html += "<div class=UiText>Auto Delete:</div>";
    html += "<div><input type=checkbox id=AddSomeone_AutoDelete "+ ((buddyJson.AutoDelete == true)? "checked" : "" ) +"><label for=AddSomeone_AutoDelete>"+ lang.yes +"</label></div>";

    // TODO, add option to delete data, etc, etc
    html += "<div class=UiText><button onclick=\"RemoveBuddy('"+ buddyObj.identity +"')\" class=\"UiDeleteButton\"><i class=\"fa fa-trash\"></i> "+ lang.delete_buddy +"</button></div>";

    html += "</div>"

    OpenWindow(html, lang.edit, 480, 640, false, true, lang.save, function(){

        if($("#AddSomeone_Name").val() == "") return;
        if($("#AddSomeone_Subscribe").is(':checked')){
            if($("#AddSomeone_Exten").val() != "" && $("#AddSomeone_SubscribeUser").val() == ""){
                $("#AddSomeone_SubscribeUser").val($("#AddSomeone_Exten").val());
            }
        }

        buddyJson.LastActivity = utcDateNow();
        buddyObj.lastActivity = buddyJson.LastActivity;

        buddyJson.DisplayName = $("#AddSomeone_Name").val();
        buddyObj.CallerIDName = buddyJson.DisplayName;

        buddyJson.Description = $("#AddSomeone_Desc").val();
        buddyObj.Desc = buddyJson.Description;

        buddyJson.MobileNumber = $("#AddSomeone_Mobile").val();
        buddyObj.MobileNumber = buddyJson.MobileNumber;

        buddyJson.Email = $("#AddSomeone_Email").val();
        buddyObj.Email = buddyJson.Email;

        buddyJson.ContactNumber1 = $("#AddSomeone_Num1").val();
        buddyObj.ContactNumber1 = buddyJson.ContactNumber1;

        buddyJson.ContactNumber2 = $("#AddSomeone_Num2").val();
        buddyObj.ContactNumber2 = buddyJson.ContactNumber2;

        buddyJson.EnableDuringDnd = $("#AddSomeone_Dnd").is(':checked');
        buddyObj.EnableDuringDnd = buddyJson.EnableDuringDnd;

        buddyJson.AutoDelete = $("#AddSomeone_AutoDelete").is(':checked');
        buddyObj.AllowAutoDelete = buddyJson.AutoDelete;

        if(buddyJson.Type == "extension" || buddyJson.Type == "xmpp"){
            // First Unsubscribe old information
            UnsubscribeBuddy(buddyObj);

            buddyJson.ExtensionNumber = $("#AddSomeone_Exten").val();
            buddyObj.ExtNo = buddyJson.ExtensionNumber;

            buddyJson.Subscribe = $("#AddSomeone_Subscribe").is(':checked');
            buddyObj.EnableSubscribe = buddyJson.Subscribe;

            if(buddyJson.Subscribe == true){
                var SubscribeUser = $("#AddSomeone_SubscribeUser").val();
                buddyJson.SubscribeUser = SubscribeUser;
                buddyObj.SubscribeUser = SubscribeUser;

                // Subscribe Actions
                SubscribeBuddy(buddyObj);
            }
        }

        // Update Visible Elements
        UpdateBuddyList();

        // Update Image
        var constraints = {
            type: 'base64',
            size: 'viewport',
            format: 'webp',  // png
            quality: 0.5,
            circle: false
        }
        $("#ImageCanvas").croppie('result', constraints).then(function(base64) {
            // Image processing done
            if(buddyJson.Type == "extension"){
                console.log("Saving image for extension buddy:", buddyJson.uID)
                localDB.setItem("img-"+ buddyJson.uID +"-extension", base64);
                // Update Images
                $("#contact-"+ buddyJson.uID +"-picture").css("background-image", 'url('+ getPicture(buddyJson.uID, 'extension', true) +')');
                $("#contact-"+ buddyJson.uID +"-picture-main").css("background-image", 'url('+ getPicture(buddyJson.uID, 'extension', true) +')');
            }
            else if(buddyJson.Type == "contact") {
                console.log("Saving image for contact buddy:", buddyJson.cID)
                localDB.setItem("img-"+ buddyJson.cID +"-contact", base64);
                // Update Images
                $("#contact-"+ buddyJson.cID +"-picture").css("background-image", 'url('+ getPicture(buddyJson.cID, 'contact', true) +')');
                $("#contact-"+ buddyJson.cID +"-picture-main").css("background-image", 'url('+ getPicture(buddyJson.cID, 'contact', true) +')');
            }
            else if(buddyJson.Type == "group") {
                console.log("Saving image for group buddy:", buddyJson.gID)
                localDB.setItem("img-"+ buddyJson.gID +"-group", base64);
                // Update Images
                $("#contact-"+ buddyJson.gID +"-picture").css("background-image", 'url('+ getPicture(buddyJson.gID, 'group', true) +')');
                $("#contact-"+ buddyJson.gID +"-picture-main").css("background-image", 'url('+ getPicture(buddyJson.gID, 'group', true) +')');
            }
            // Update
            UpdateBuddyList();
        });

        // Update:
        json.DataCollection[itemId] = buddyJson;

        // Save To DB
        localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));

        CloseWindow();
    }, lang.cancel, function(){
        CloseWindow();
    }, function(){
        // DoOnLoad
        cropper = $("#ImageCanvas").croppie({
            viewport: { width: 150, height: 150, type: 'circle' }
        });

        // Preview Existing Image
        if(buddyJson.Type == "extension"){
            $("#ImageCanvas").croppie('bind', { url: getPicture(buddyJson.uID, "extension") }).then();


        }
        if(buddyJson.Type == "xmpp"){
            $("#ImageCanvas").croppie('bind', { url: getPicture(buddyJson.uID, "xmpp") }).then();

            $("#fileUploader").hide();
            $("#AddSomeone_Name").attr("disabled", true);
            $("#AddSomeone_Desc").attr("disabled", true);
            $("#AddSomeone_Mobile").attr("disabled", true);
            $("#AddSomeone_Email").attr("disabled", true);
            $("#AddSomeone_Num1").attr("disabled", true);
            $("#AddSomeone_Num2").attr("disabled", true);
        }
        else if(buddyJson.Type == "contact") {
            $("#ImageCanvas").croppie('bind', { url: getPicture(buddyJson.cID, "contact") }).then();
        }
        else if(buddyJson.Type == "group") {
            $("#ImageCanvas").croppie('bind', { url: getPicture(buddyJson.gID, "group") }).then();
        }

        $("#AddSomeone_Subscribe").change(function(){
            if($("#AddSomeone_Subscribe").is(':checked')){
                if($("#AddSomeone_Exten").val() != "" && $("#AddSomeone_SubscribeUser").val() == ""){
                    $("#AddSomeone_SubscribeUser").val($("#AddSomeone_Exten").val());
                }
                $("#RowSubscribe").show();
            } else {
                $("#RowSubscribe").hide();
            }
        });

        // Wire-up File Change
        $("#fileUploader").change(function () {
            var filesArray = $(this).prop('files');

            if (filesArray.length == 1) {
                var uploadId = Math.floor(Math.random() * 1000000000);
                var fileObj = filesArray[0];
                var fileName = fileObj.name;
                var fileSize = fileObj.size;

                if (fileSize <= 52428800) {
                    console.log("Adding (" + uploadId + "): " + fileName + " of size: " + fileSize + "bytes");

                    var reader = new FileReader();
                    reader.Name = fileName;
                    reader.UploadId = uploadId;
                    reader.Size = fileSize;
                    reader.onload = function (event) {
                        $("#ImageCanvas").croppie('bind', {
                            url: event.target.result
                        });
                    }
                    reader.readAsDataURL(fileObj);
                }
                else {
                    Alert(lang.alert_file_size, lang.error);
                }
            }
            else {
                Alert(lang.alert_single_file, lang.error);
            }
        });
    });
}
function SetStatusWindow(){
    HidePopup();

    var windowHtml = "<div class=UiWindowField>";
    windowHtml += "<div><input type=text id=presence_text class=UiInputText maxlength=128></div>";
    windowHtml += "</div>";
    OpenWindow(windowHtml, lang.set_status, 180, 350, false, false, "OK", function(){
        // ["away", "chat", "dnd", "xa"] => ["Away", "Available", "Busy", "Gone"]

        var presenceStr = "chat"
        var statusStr = $("#presence_text").val();

        localDB.setItem("XmppLastPresence", presenceStr);
        localDB.setItem("XmppLastStatus", statusStr);

        XmppSetMyPresence(presenceStr, statusStr);

        CloseWindow();
    }, "Cancel", function(){
        CloseWindow();
    }, function(){
        $("#presence_text").val(getDbItem("XmppLastStatus", ""));
    });
}

// Init UI
// =======
function InitUi(NumberToBeCalled){

    var phone = $("#Phone");
    phone.empty();

    var rightSection = $("<div/>");
    rightSection.attr("id", "rightContent");
    phone.append(rightSection);

    UpdateUI();

    // Check if you account is created
    if(profileUserID == null ){
        ShowMyProfile();
        return; // Don't load any more, after applying settings, the page must reload.
    }

    // Custom Web hook
    if(typeof web_hook_on_init !== 'undefined') web_hook_on_init();

    CreateUserAgent(NumberToBeCalled);
}



// Create User Agent
// =================
function CreateUserAgent(NumberToBeCalled) {
    console.log("Creating User Agent...");
    if(SipDomain==null || SipDomain=="" || SipDomain=="null" || SipDomain=="undefined") SipDomain = wssServer; // Sets globally
    var options = {
        uri: SIP.UserAgent.makeURI("sip:"+ SipUsername + "@" + SipDomain),
        transportOptions: {
            server: "wss://" + wssServer + ":"+ WebSocketPort +""+ ServerPath,
            traceSip: false,
            connectionTimeout: TransportConnectionTimeout
            // keepAliveInterval: 30 // Uncomment this and make this any number greater then 0 for keep alive...
            // NB, adding a keep alive will NOT fix bad internet, if your connection cannot stay open (permanent WebSocket Connection) you probably
            // have a router or ISP issue, and if your internet is so poor that you need to some how keep it alive with empty packets
            // upgrade you internet connection. This is voip we are talking about here.
        },
        sessionDescriptionHandlerFactoryOptions: {
            peerConnectionConfiguration :{
                bundlePolicy: BundlePolicy,
                // certificates: undefined,
                // iceCandidatePoolSize: 10,
                // iceServers: [{ urls: "stun:stun.l.google.com:19302" }],
                // iceTransportPolicy: "all",
                // peerIdentity: undefined,
                // rtcpMuxPolicy: "require",
            },
            iceGatheringTimeout: IceStunCheckTimeout
        },
        contactName: ContactUserName,
        displayName: profileName,
        authorizationUsername: SipUsername,
        authorizationPassword: SipPassword,
        hackIpInContact: IpInContact,           // Asterisk should also be set to rewrite contact
        userAgentString: userAgentStr,
        autoStart: false,
        autoStop: true,
        register: false,
        noAnswerTimeout: NoAnswerTimeout,
        // sipExtension100rel: // UNSUPPORTED | SUPPORTED | REQUIRED NOTE: rel100 is not supported
        contactParams: {},
        delegate: {
            onInvite: function (sip){
                ReceiveCall(sip);
            },
            onMessage: function (sip){
                ReceiveOutOfDialogMessage(sip);
            }
        }
    }
    if(IceStunServerJson != ""){
        options.sessionDescriptionHandlerFactoryOptions.peerConnectionConfiguration.iceServers = JSON.parse(IceStunServerJson);
    }

    // Added to the contact BEFORE the '>' (permanent)
    if(RegisterContactParams && RegisterContactParams != "" && RegisterContactParams != "{}"){
        try{
            options.contactParams = JSON.parse(RegisterContactParams);
        } catch(e){}
    }
    if(WssInTransport){
        try{
            options.contactParams.transport = "wss";
        } catch(e){}
    }

    // Add (Hardcode) other RTCPeerConnection({ rtcConfiguration }) config dictionary options here
    // https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/RTCPeerConnection
    // Example:
    // options.sessionDescriptionHandlerFactoryOptions.peerConnectionConfiguration.rtcpMuxPolicy = "require";

    userAgent = new SIP.UserAgent(options);
    userAgent.isRegistered = function(){
        return (userAgent && userAgent.registerer && userAgent.registerer.state == SIP.RegistererState.Registered);
    }
    // For some reason this is marked as private... not sure why
    userAgent.sessions = userAgent._sessions;
    userAgent.registrationCompleted = false;
    userAgent.registering = false;
    userAgent.transport.ReconnectionAttempts = TransportReconnectionAttempts;
    userAgent.transport.attemptingReconnection = false;
    userAgent.BlfSubs = [];
    userAgent.lastVoicemailCount = 0;

    console.log("Creating User Agent... Done");

    userAgent.transport.onConnect = function(){
        onTransportConnected();
    }
    userAgent.transport.onDisconnect = function(error){
        if(error){
            onTransportConnectError(error);
        }
        else {
            onTransportDisconnected();
        }
    }

    var RegistererOptions = {
        expires: RegisterExpires,
        extraHeaders: [],
        extraContactHeaderParams: []
    }

    // Added to the SIP Headers
    if(RegisterExtraHeaders && RegisterExtraHeaders != "" && RegisterExtraHeaders != "{}"){
        try{
            var registerExtraHeaders = JSON.parse(RegisterExtraHeaders);
            for (const [key, value] of Object.entries(registerExtraHeaders)) {
                if(value != ""){
                    RegistererOptions.extraHeaders.push(key + ": "+  value);
                }
            }
        } catch(e){}
    }

    // Added to the contact AFTER the '>' (not permanent)
    if(RegisterExtraContactParams && RegisterExtraContactParams != "" && RegisterExtraContactParams != "{}"){
        try{
            var registerExtraContactParams = JSON.parse(RegisterExtraContactParams);
            for (const [key, value] of Object.entries(registerExtraContactParams)) {
                if(value == ""){
                    RegistererOptions.extraContactHeaderParams.push(key);
                } else {
                    RegistererOptions.extraContactHeaderParams.push(key + ":"+  value);
                }
            }
        } catch(e){}
    }

    userAgent.registerer = new SIP.Registerer(userAgent, RegistererOptions);
    console.log("Creating Registerer... Done");

    userAgent.registerer.stateChange.addListener(function(newState){
        console.log("User Agent Registration State:", newState);
        switch (newState) {
            case SIP.RegistererState.Initial:
                // Nothing to do
                break;
            case SIP.RegistererState.Registered:
                onRegistered(NumberToBeCalled);
                break;
            case SIP.RegistererState.Unregistered:
                onUnregistered();
                break;
            case SIP.RegistererState.Terminated:
                // Nothing to do
                break;
        }
    });

    console.log("User Agent Connecting to WebSocket...");
    $("#regStatus").html(lang.connecting_to_web_socket);
    userAgent.start().catch(function(error){
        onTransportConnectError(error);
    });
}

// Transport Events
// ================
function onTransportConnected(){
    console.log("Connected to Web Socket!");
    $("#regStatus").html(lang.connected_to_web_socket);

    $("#WebRtcFailed").hide();

    // Reset the ReconnectionAttempts
    userAgent.isReRegister = false;
    userAgent.transport.attemptingReconnection = false;
    userAgent.transport.ReconnectionAttempts = TransportReconnectionAttempts;

    // Auto start register
    if(userAgent.transport.attemptingReconnection == false && userAgent.registering == false){
        window.setTimeout(function (){
            Register();
        }, 500);
    } else{
        console.warn("onTransportConnected: Register() called, but attemptingReconnection is true or registering is true")
    }
}
function onTransportConnectError(error){
    console.warn("WebSocket Connection Failed:", error);

    // We set this flag here so that the re-register attempts are fully completed.
    userAgent.isReRegister = false;

    // If there is an issue with the WS connection
    // We unregister, so that we register again once its up
    console.log("Unregister...");
    try{
        userAgent.registerer.unregister();
    } catch(e){
        // I know!!!
    }

    $("#regStatus").html(lang.web_socket_error);
    $("#WebRtcFailed").show();

    ReconnectTransport();

    // Custom Web hook
    if(typeof web_hook_on_transportError !== 'undefined') web_hook_on_transportError(userAgent.transport, userAgent);
}
function onTransportDisconnected(){
    console.log("Disconnected from Web Socket!");
    $("#regStatus").html(lang.disconnected_from_web_socket);

    userAgent.isReRegister = false;
}
function ReconnectTransport(){
    if(userAgent == null) return;

    userAgent.registering = false; // if the transport was down, you will not be registered
    if(userAgent.transport && userAgent.transport.isConnected()){
        // Asked to re-connect, but ws is connected
        onTransportConnected();
        return;
    }
    console.log("Reconnect Transport...");

    window.setTimeout(function(){
        $("#regStatus").html(lang.connecting_to_web_socket);
        console.log("ReConnecting to WebSocket...");

        if(userAgent.transport && userAgent.transport.isConnected()){
            // Already Connected
            onTransportConnected();
            return;
        } else {
            userAgent.transport.attemptingReconnection = true
            userAgent.reconnect().catch(function(error){
                userAgent.transport.attemptingReconnection = false
                console.warn("Failed to reconnect", error);

                // Try Again
                ReconnectTransport();
            });
        }
    }, TransportReconnectionTimeout * 1000);

    $("#regStatus").html(lang.connecting_to_web_socket);
    console.log("Waiting to Re-connect...", TransportReconnectionTimeout, "Attempt remaining", userAgent.transport.ReconnectionAttempts);
    userAgent.transport.ReconnectionAttempts = userAgent.transport.ReconnectionAttempts - 1;
}

// Registration
// ============
function Register() {
    if (userAgent == null) return;
    if (userAgent.registering == true) return;
    if (userAgent.isRegistered()) return;

    var RegistererRegisterOptions = {
        requestDelegate: {
            onReject: function(sip){
                onRegisterFailed(sip.message.reasonPhrase, sip.message.statusCode);
            }
        }
    }

    console.log("Sending Registration...");
    $("#regStatus").html(lang.sending_registration);
    userAgent.registering = true
    userAgent.registerer.register(RegistererRegisterOptions);
}
function Unregister(skipUnsubscribe) {
    if (userAgent == null || !userAgent.isRegistered()) return;

    if(skipUnsubscribe == true){
        console.log("Skipping Unsubscribe");
    } else {
        console.log("Unsubscribing...");
        $("#regStatus").html(lang.unsubscribing);
        try {
            UnsubscribeAll();
        } catch (e) { }
    }

    console.log("Unregister...");
    $("#regStatus").html(lang.disconnecting);
    userAgent.registerer.unregister();

    userAgent.transport.attemptingReconnection = false;
    userAgent.registering = false;
    userAgent.isReRegister = false;
}

// Registration Events
// ===================
/**
 * Called when account is registered
 */
function onRegistered(NumberToBeCalled){
    // This code fires on re-register after session timeout
    // to ensure that events are not fired multiple times
    // a isReRegister state is kept.
    // TODO: This check appears obsolete

    userAgent.registrationCompleted = true;
    if(!userAgent.isReRegister) {
        console.log("Registered!");

        $("#reglink").hide();
        $("#dereglink").show();
        if(DoNotDisturbEnabled || DoNotDisturbPolicy == "enabled") {
            $("#dereglink").attr("class", "dotDoNotDisturb");
            $("#dndStatus").html("(DND)");
        }

        // Start Subscribe Loop
        window.setTimeout(function (){
            SubscribeAll();
        }, 500);

        // Output to status
        $("#regStatus").html(lang.registered);
        DialByLine('audio' ,NumberToBeCalled);
        // Start XMPP
        if(ChatEngine == "XMPP") reconnectXmpp();

        userAgent.registering = false;

        // Close possible Alerts that may be open. (Can be from failed registers)
        if (alertObj != null) {
            alertObj.dialog("close");
            alertObj = null;
        }

        // Custom Web hook
        if(typeof web_hook_on_register !== 'undefined') web_hook_on_register(userAgent);
    }
    else {
        userAgent.registering = false;

        console.log("ReRegistered!");
    }
    userAgent.isReRegister = true;
}
/**
 * Called if UserAgent can connect, but not register.
 * @param {string} response Incoming request message
 * @param {string} cause Cause message. Unused
**/
function onRegisterFailed(response, cause){
    console.log("Registration Failed: " + response);
    $("#regStatus").html(lang.registration_failed);

    $("#reglink").show();
    $("#dereglink").hide();

    Alert(lang.registration_failed +":"+ response, lang.registration_failed);

    userAgent.registering = false;

    // Custom Web hook
    if(typeof web_hook_on_registrationFailed !== 'undefined') web_hook_on_registrationFailed(response);
}
/**
 * Called when Unregister is requested
 */
function onUnregistered(){
    if(userAgent.registrationCompleted){
        console.log("Unregistered, bye!");
        $("#regStatus").html(lang.unregistered);

        $("#reglink").show();
        $("#dereglink").hide();

        // Custom Web hook
        if(typeof web_hook_on_unregistered !== 'undefined') web_hook_on_unregistered();
    }
    else {
        // Was never really registered, so cant really say unregistered
    }

    // We set this flag here so that the re-register attempts are fully completed.
    userAgent.isReRegister = false;
}

// Inbound Calls
// =============
function ReceiveCall(session) {
    var callerID = session.remoteIdentity.displayName;
    var did = session.remoteIdentity.uri.user;
    if (typeof callerID === 'undefined') callerID = did;

    console.log("New Incoming Call!", callerID +" <"+ did +">");

    var CurrentCalls = countSessions(session.id);
    console.log("Current Call Count:", CurrentCalls);

    var buddyObj = FindBuddyByDid(did);
    // Make new contact of its not there
    if(buddyObj == null) {

        // Check if Privacy DND is enabled

        var buddyType = (did.length > DidLength)? "contact" : "extension";
        var focusOnBuddy = (CurrentCalls==0);
        buddyObj = MakeBuddy(buddyType, true, focusOnBuddy, false, callerID, did, null, false, null, AutoDeleteDefault);
    }
    else {
        // Double check that the buddy has the same caller ID as the incoming call
        // With Buddies that are contacts, eg +441234567890 <+441234567890> leave as as
        if(buddyObj.type == "extension" && buddyObj.CallerIDName != callerID){
            UpdateBuddyCallerID(buddyObj, callerID);
        }
        else if(buddyObj.type == "contact" && callerID != did && buddyObj.CallerIDName != callerID){
            UpdateBuddyCallerID(buddyObj, callerID);
        }
    }

    var startTime = moment.utc();

    // Create the line and add the session so we can answer or reject it.
    newLineNumber = newLineNumber + 1;
    var lineObj = new Line(newLineNumber, callerID, did, buddyObj);
    lineObj.SipSession = session;
    lineObj.SipSession.data = {}
    lineObj.SipSession.data.line = lineObj.LineNumber;
    lineObj.SipSession.data.calldirection = "inbound";
    lineObj.SipSession.data.terminateby = "";
    lineObj.SipSession.data.src = did;
    lineObj.SipSession.data.buddyId = lineObj.BuddyObj.identity;
    lineObj.SipSession.data.callstart = startTime.format("YYYY-MM-DD HH:mm:ss UTC");
    lineObj.SipSession.data.callTimer = window.setInterval(function(){
        var now = moment.utc();
        var duration = moment.duration(now.diff(startTime));
        var timeStr = formatShortDuration(duration.asSeconds());
        $("#line-" + lineObj.LineNumber + "-timer").html(timeStr);
        $("#line-" + lineObj.LineNumber + "-datetime").html(timeStr);
    }, 1000);
    lineObj.SipSession.data.earlyReject = false;
    Lines.push(lineObj);
    // Detect Video
    lineObj.SipSession.data.withvideo = false;
    if(EnableVideoCalling == true && lineObj.SipSession.request.body){
        // Asterisk 13 PJ_SIP always sends m=video if endpoint has video codec,
        // even if original invite does not specify video.
        if(lineObj.SipSession.request.body.indexOf("m=video") > -1) {
            lineObj.SipSession.data.withvideo = true;
            // The invite may have video, but the buddy may be a contact
            if(buddyObj.type == "contact"){
                // videoInvite = false;
                // TODO: Is this limitation necessary?
            }
        }
    }

    // Session Delegates
    lineObj.SipSession.delegate = {
        onBye: function(sip){
            onSessionReceivedBye(lineObj, sip)
        },
        onMessage: function(sip){
            onSessionReceivedMessage(lineObj, sip);
        },
        onInvite: function(sip){
            onSessionReinvited(lineObj, sip);
        },
        onSessionDescriptionHandler: function(sdh, provisional){
            onSessionDescriptionHandlerCreated(lineObj, sdh, provisional, lineObj.SipSession.data.withvideo);
        }
    }
    // incomingInviteRequestDelegate
    lineObj.SipSession.incomingInviteRequest.delegate = {
        onCancel: function(sip){
            onInviteCancel(lineObj, sip)
        }
    }

    // Possible Early Rejection options
    if(DoNotDisturbEnabled == true || DoNotDisturbPolicy == "enabled") {
        if(DoNotDisturbEnabled == true && buddyObj.EnableDuringDnd == true){
            // This buddy has been allowed
            console.log("Buddy is allowed to call while you are on DND")
        }
        else {
            console.log("Do Not Disturb Enabled, rejecting call.");
            lineObj.SipSession.data.earlyReject = true;
            RejectCall(lineObj.LineNumber, true);
            return;
        }
    }
    if(CurrentCalls >= 1){
        if(CallWaitingEnabled == false || CallWaitingEnabled == "disabled"){
            console.log("Call Waiting Disabled, rejecting call.");
            lineObj.SipSession.data.earlyReject = true;
            RejectCall(lineObj.LineNumber, true);
            return;
        }
    }

    // Create the call HTML
    AddLineHtml(lineObj, "inbound");
    $("#line-" + lineObj.LineNumber + "-msg").html(lang.incoming_call);
    $("#line-" + lineObj.LineNumber + "-msg").show();
    $("#line-" + lineObj.LineNumber + "-timer").show();
    if(lineObj.SipSession.data.withvideo){
        $("#line-"+ lineObj.LineNumber +"-answer-video").show();
    }
    else {
        $("#line-"+ lineObj.LineNumber +"-answer-video").hide();
    }
    $("#line-" + lineObj.LineNumber + "-AnswerCall").show();

    // Update the buddy list now so that any early rejected calls don't flash on
    UpdateBuddyList();

    // Auto Answer options
    var autoAnswerRequested = false;
    var answerTimeout = 1000;
    if (!AutoAnswerEnabled  && IntercomPolicy == "enabled"){ // Check headers only if policy is allow

        // https://github.com/InnovateAsterisk/Browser-Phone/issues/126
        // Alert-Info: info=alert-autoanswer
        // Alert-Info: answer-after=0
        // Call-info: answer-after=0; x=y
        // Call-Info: Answer-After=0
        // Alert-Info: ;info=alert-autoanswer
        // Alert-Info: <sip:>;info=alert-autoanswer
        // Alert-Info: <sip:domain>;info=alert-autoanswer

        var ci = session.request.headers["Call-Info"];
        if (ci !== undefined && ci.length > 0){
            for (var i = 0; i < ci.length; i++){
                var raw_ci = ci[i].raw.toLowerCase();
                if (raw_ci.indexOf("answer-after=") > 0){
                    var temp_seconds_autoanswer = parseInt(raw_ci.substring(raw_ci.indexOf("answer-after=") +"answer-after=".length).split(';')[0]);
                    if (Number.isInteger(temp_seconds_autoanswer) && temp_seconds_autoanswer >= 0){
                        autoAnswerRequested = true;
                        if(temp_seconds_autoanswer > 1) answerTimeout = temp_seconds_autoanswer * 1000;
                        break;
                    }
                }
            }
        }
        var ai = session.request.headers["Alert-Info"];
        if (autoAnswerRequested === false && ai !== undefined && ai.length > 0){
            for (var i=0; i < ai.length ; i++){
                var raw_ai = ai[i].raw.toLowerCase();
                if (raw_ai.indexOf("auto answer") > 0 || raw_ai.indexOf("alert-autoanswer") > 0){
                    var autoAnswerRequested = true;
                    break;
                }
                if (raw_ai.indexOf("answer-after=") > 0){
                    var temp_seconds_autoanswer = parseInt(raw_ai.substring(raw_ai.indexOf("answer-after=") +"answer-after=".length).split(';')[0]);
                    if (Number.isInteger(temp_seconds_autoanswer) && temp_seconds_autoanswer >= 0){
                        autoAnswerRequested = true;
                        if(temp_seconds_autoanswer > 1) answerTimeout = temp_seconds_autoanswer * 1000;
                        break;
                    }
                }
            }
        }
    }

    if(AutoAnswerEnabled || AutoAnswerPolicy == "enabled" || autoAnswerRequested){
        if(CurrentCalls == 0){ // There are no other calls, so you can answer
            console.log("Going to Auto Answer this call...");
            window.setTimeout(function(){
                // If the call is with video, assume the auto answer is also
                // In order for this to work nicely, the recipient maut be "ready" to accept video calls
                // In order to ensure video call compatibility (i.e. the recipient must have their web cam in, and working)
                // The NULL video should be configured
                // https://github.com/InnovateAsterisk/Browser-Phone/issues/26
                if(lineObj.SipSession.data.withvideo) {
                    AnswerVideoCall(lineObj.LineNumber);
                }
                else {
                    AnswerAudioCall(lineObj.LineNumber);
                }
            }, answerTimeout);

            // Select Buddy
            SelectLine(lineObj.LineNumber);
            return;
        }
        else {
            console.warn("Could not auto answer call, already on a call.");
        }
    }

    // Check if that buddy is not already on a call??
    var streamVisible = $("#stream-"+ buddyObj.identity).is(":visible");
    if (streamVisible || CurrentCalls == 0) {
        // If you are already on the selected buddy who is now calling you, switch to his call.
        // NOTE: This will put other calls on hold
        if(CurrentCalls == 0) SelectLine(lineObj.LineNumber);
    }

    // Show notification / Ring / Windows Etc
    // ======================================

    // Browser Window Notification
    if ("Notification" in window) {
        if (Notification.permission === "granted") {
            var noticeOptions = { body: lang.incoming_call_from +" " + callerID +" <"+ did +">", icon: getPicture(buddyObj.identity) }
            var inComingCallNotification = new Notification(lang.incoming_call, noticeOptions);
            inComingCallNotification.onclick = function (event) {

                var lineNo = lineObj.LineNumber;
                var videoInvite = lineObj.SipSession.data.withvideo
                window.setTimeout(function(){
                    // https://github.com/InnovateAsterisk/Browser-Phone/issues/26
                    if(videoInvite) {
                        AnswerVideoCall(lineNo)
                    }
                    else {
                        AnswerAudioCall(lineNo);
                    }
                }, 1000);

                // Select Buddy
                SelectLine(lineNo);
                return;
            }
        }
    }

    // Play Ring Tone if not on the phone
    if(EnableRingtone == true){
        if(CurrentCalls >= 1){
            // Play Alert
            console.log("Audio:", audioBlobs.CallWaiting.url);
            var ringer = new Audio(audioBlobs.CallWaiting.blob);
            ringer.preload = "auto";
            ringer.loop = false;
            ringer.oncanplaythrough = function(e) {
                if (typeof ringer.sinkId !== 'undefined' && getRingerOutputID() != "default") {
                    ringer.setSinkId(getRingerOutputID()).then(function() {
                        console.log("Set sinkId to:", getRingerOutputID());
                    }).catch(function(e){
                        console.warn("Failed not apply setSinkId.", e);
                    });
                }
                // If there has been no interaction with the page at all... this page will not work
                ringer.play().then(function(){
                    // Audio Is Playing
                }).catch(function(e){
                    console.warn("Unable to play audio file.", e);
                });
            }
            lineObj.SipSession.data.ringerObj = ringer;
        } else {
            // Play Ring Tone
            console.log("Audio:", audioBlobs.Ringtone.url);
            var ringer = new Audio(audioBlobs.Ringtone.blob);
            ringer.preload = "auto";
            ringer.loop = true;
            ringer.oncanplaythrough = function(e) {
                if (typeof ringer.sinkId !== 'undefined' && getRingerOutputID() != "default") {
                    ringer.setSinkId(getRingerOutputID()).then(function() {
                        console.log("Set sinkId to:", getRingerOutputID());
                    }).catch(function(e){
                        console.warn("Failed not apply setSinkId.", e);
                    });
                }
                // If there has been no interaction with the page at all... this page will not work
                ringer.play().then(function(){
                    // Audio Is Playing
                }).catch(function(e){
                    console.warn("Unable to play audio file.", e);
                });
            }
            lineObj.SipSession.data.ringerObj = ringer;
        }

    }

    // Custom Web hook
    if(typeof web_hook_on_invite !== 'undefined') web_hook_on_invite(session);
}
function AnswerAudioCall(lineNumber) {
    // CloseWindow();

    var lineObj = FindLineByNumber(lineNumber);
    if(lineObj == null){
        console.warn("Failed to get line ("+ lineNumber +")");
        return;
    }
    var session = lineObj.SipSession;
    // Stop the ringtone
    if(session.data.ringerObj){
        session.data.ringerObj.pause();
        session.data.ringerObj.removeAttribute('src');
        session.data.ringerObj.load();
        session.data.ringerObj = null;
    }
    // Check vitals
    if(HasAudioDevice == false){
        Alert(lang.alert_no_microphone);
        $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_failed);
        $("#line-" + lineObj.LineNumber + "-AnswerCall").hide();
        return;
    }

    // Update UI
    $("#line-" + lineObj.LineNumber + "-AnswerCall").hide();

    // Start SIP handling
    var supportedConstraints = navigator.mediaDevices.getSupportedConstraints();
    var spdOptions = {
        sessionDescriptionHandlerOptions: {
            constraints: {
                audio: { deviceId : "default" },
                video: false
            }
        }
    }

    // Configure Audio
    var currentAudioDevice = getAudioSrcID();
    if(currentAudioDevice != "default"){
        var confirmedAudioDevice = false;
        for (var i = 0; i < AudioinputDevices.length; ++i) {
            if(currentAudioDevice == AudioinputDevices[i].deviceId) {
                confirmedAudioDevice = true;
                break;
            }
        }
        if(confirmedAudioDevice) {
            spdOptions.sessionDescriptionHandlerOptions.constraints.audio.deviceId = { exact: currentAudioDevice }
        }
        else {
            console.warn("The audio device you used before is no longer available, default settings applied.");
            localDB.setItem("AudioSrcId", "default");
        }
    }
    // Add additional Constraints
    if(supportedConstraints.autoGainControl) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.autoGainControl = AutoGainControl;
    }
    if(supportedConstraints.echoCancellation) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.echoCancellation = EchoCancellation;
    }
    if(supportedConstraints.noiseSuppression) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.noiseSuppression = NoiseSuppression;
    }

    // Save Devices
    lineObj.SipSession.data.withvideo = false;
    lineObj.SipSession.data.VideoSourceDevice = null;
    lineObj.SipSession.data.AudioSourceDevice = getAudioSrcID();
    lineObj.SipSession.data.AudioOutputDevice = getAudioOutputID();

    // Send Answer
    lineObj.SipSession.accept(spdOptions).then(function(){
        onInviteAccepted(lineObj,false);
    }).catch(function(error){
        console.warn("Failed to answer call", error, lineObj.SipSession);
        lineObj.SipSession.data.reasonCode = 500;
        lineObj.SipSession.data.reasonText = "Client Error";
        teardownSession(lineObj);
    });
}
function AnswerVideoCall(lineNumber) {
    // CloseWindow();

    var lineObj = FindLineByNumber(lineNumber);
    if(lineObj == null){
        console.warn("Failed to get line ("+ lineNumber +")");
        return;
    }
    var session = lineObj.SipSession;
    // Stop the ringtone
    if(session.data.ringerObj){
        session.data.ringerObj.pause();
        session.data.ringerObj.removeAttribute('src');
        session.data.ringerObj.load();
        session.data.ringerObj = null;
    }
    // Check vitals
    if(HasAudioDevice == false){
        Alert(lang.alert_no_microphone);
        $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_failed);
        $("#line-" + lineObj.LineNumber + "-AnswerCall").hide();
        return;
    }

    // Update UI
    $("#line-" + lineObj.LineNumber + "-AnswerCall").hide();

    // Start SIP handling
    var supportedConstraints = navigator.mediaDevices.getSupportedConstraints();
    var spdOptions = {
        sessionDescriptionHandlerOptions: {
            constraints: {
                audio: { deviceId : "default" },
                video: { deviceId : "default" }
            }
        }
    }

    // Configure Audio
    var currentAudioDevice = getAudioSrcID();
    if(currentAudioDevice != "default"){
        var confirmedAudioDevice = false;
        for (var i = 0; i < AudioinputDevices.length; ++i) {
            if(currentAudioDevice == AudioinputDevices[i].deviceId) {
                confirmedAudioDevice = true;
                break;
            }
        }
        if(confirmedAudioDevice) {
            spdOptions.sessionDescriptionHandlerOptions.constraints.audio.deviceId = { exact: currentAudioDevice }
        }
        else {
            console.warn("The audio device you used before is no longer available, default settings applied.");
            localDB.setItem("AudioSrcId", "default");
        }
    }
    // Add additional Constraints
    if(supportedConstraints.autoGainControl) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.autoGainControl = AutoGainControl;
    }
    if(supportedConstraints.echoCancellation) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.echoCancellation = EchoCancellation;
    }
    if(supportedConstraints.noiseSuppression) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.noiseSuppression = NoiseSuppression;
    }

    // Configure Video
    var currentVideoDevice = getVideoSrcID();
    if(currentVideoDevice != "default"){
        var confirmedVideoDevice = false;
        for (var i = 0; i < VideoinputDevices.length; ++i) {
            if(currentVideoDevice == VideoinputDevices[i].deviceId) {
                confirmedVideoDevice = true;
                break;
            }
        }
        if(confirmedVideoDevice){
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.deviceId = { exact: currentVideoDevice }
        }
        else {
            console.warn("The video device you used before is no longer available, default settings applied.");
            localDB.setItem("VideoSrcId", "default"); // resets for later and subsequent calls
        }
    }
    // Add additional Constraints
    if(supportedConstraints.frameRate && maxFrameRate != "") {
        spdOptions.sessionDescriptionHandlerOptions.constraints.video.frameRate = maxFrameRate;
    }
    if(supportedConstraints.height && videoHeight != "") {
        spdOptions.sessionDescriptionHandlerOptions.constraints.video.height = videoHeight;
    }
    if(supportedConstraints.aspectRatio && videoAspectRatio != "") {
        spdOptions.sessionDescriptionHandlerOptions.constraints.video.aspectRatio = videoAspectRatio;
    }

    // Save Devices
    lineObj.SipSession.data.withvideo = true;
    lineObj.SipSession.data.VideoSourceDevice = getVideoSrcID();
    lineObj.SipSession.data.AudioSourceDevice = getAudioSrcID();
    lineObj.SipSession.data.AudioOutputDevice = getAudioOutputID();

    if(StartVideoFullScreen) ExpandVideoArea(lineObj.LineNumber);

    // Send Answer
    lineObj.SipSession.accept(spdOptions).then(function(){
        onInviteAccepted(lineObj,true);
    }).catch(function(error){
        console.warn("Failed to answer call", error, lineObj.SipSession);
        lineObj.SipSession.data.reasonCode = 500;
        lineObj.SipSession.data.reasonText = "Client Error";
        teardownSession(lineObj);
    });
}
function RejectCall(lineNumber) {
    var lineObj = FindLineByNumber(lineNumber);
    if (lineObj == null) {
        console.warn("Unable to find line ("+ lineNumber +")");
        return;
    }
    var session = lineObj.SipSession;
    if (session == null) {
        console.warn("Reject failed, null session");
        $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_failed);
        $("#line-" + lineObj.LineNumber + "-AnswerCall").hide();
    }
    if(session.state == SIP.SessionState.Established){
        session.bye().catch(function(e){
            console.warn("Problem in RejectCall(), could not bye() call", e, session);
        });
    }
    else {
        session.reject({
            statusCode: 486,
            reasonPhrase: "Busy Here"
        }).catch(function(e){
            console.warn("Problem in RejectCall(), could not reject() call", e, session);
        });
    }
    $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_rejected);

    session.data.terminateby = "us";
    session.data.reasonCode = 486;
    session.data.reasonText = "Busy Here";
    teardownSession(lineObj);
}

// Session Events
// ==============

// Incoming INVITE
function onInviteCancel(lineObj, response){
        // Remote Party Canceled while ringing...

        // Check to see if this call has been completed elsewhere
        // https://github.com/InnovateAsterisk/Browser-Phone/issues/405
        var temp_cause = 0;
        var reason = response.headers["Reason"];
        if (reason !== undefined && reason.length > 0){
            for (var i = 0; i < reason.length; i++){
                var cause = reason[i].raw.toLowerCase().trim(); // Reason: Q.850 ;cause=16 ;text="Terminated"
                var items = cause.split(';');
                if (items.length >= 2 && (items[0].trim() == "sip" || items[0].trim() == "q.850") && items[1].includes("cause") && cause.includes("call completed elsewhere")){
                    temp_cause = parseInt(items[1].substring(items[1].indexOf("=")+1).trim());
                    // No sample provided for "token"
                    break;
                }
            }
        }

        lineObj.SipSession.data.terminateby = "them";
        lineObj.SipSession.data.reasonCode = temp_cause;
        if(temp_cause == 0){
            lineObj.SipSession.data.reasonText = "Call Cancelled";
            console.log("Call canceled by remote party before answer");
        } else {
            lineObj.SipSession.data.reasonText = "Call completed elsewhere";
            console.log("Call completed elsewhere before answer");
        }

        lineObj.SipSession.dispose().catch(function(error){
            console.log("Failed to dispose the cancel dialog", error);
        })

        teardownSession(lineObj);
}
// Both Incoming an outgoing INVITE
function onInviteAccepted(lineObj, includeVideo, response){
    // Call in progress
    var session = lineObj.SipSession;

    if(session.data.earlyMedia){
        session.data.earlyMedia.pause();
        session.data.earlyMedia.removeAttribute('src');
        session.data.earlyMedia.load();
        session.data.earlyMedia = null;
    }

    window.clearInterval(session.data.callTimer);
    $("#line-" + lineObj.LineNumber + "-timer").show();
    var startTime = moment.utc();
    session.data.startTime = startTime;
    session.data.callTimer = window.setInterval(function(){
        var now = moment.utc();
        var duration = moment.duration(now.diff(startTime));
        var timeStr = formatShortDuration(duration.asSeconds());
        $("#line-" + lineObj.LineNumber + "-timer").html(timeStr);
        $("#line-" + lineObj.LineNumber + "-datetime").html(timeStr);
    }, 1000);
    session.isOnHold = false;
    session.data.started = true;

    if(includeVideo){
        // Preview our stream from peer connection
        var localVideoStream = new MediaStream();
        var pc = session.sessionDescriptionHandler.peerConnection;
        pc.getSenders().forEach(function (sender) {
            if(sender.track && sender.track.kind == "video"){
                localVideoStream.addTrack(sender.track);
            }
        });
        var localVideo = $("#line-" + lineObj.LineNumber + "-localVideo").get(0);
        localVideo.srcObject = localVideoStream;
        localVideo.onloadedmetadata = function(e) {
            localVideo.play();
        }

        // Apply Call Bandwidth Limits
        if(MaxVideoBandwidth > -1){
            pc.getSenders().forEach(function (sender) {
                if(sender.track && sender.track.kind == "video"){

                    var parameters = sender.getParameters();
                    if(!parameters.encodings) parameters.encodings = [{}];
                    parameters.encodings[0].maxBitrate = MaxVideoBandwidth * 1000;

                    console.log("Applying limit for Bandwidth to: ", MaxVideoBandwidth + "kb per second")

                    // Only going to try without re-negotiations
                    sender.setParameters(parameters).catch(function(e){
                        console.warn("Cannot apply Bandwidth Limits", e);
                    });

                }
            });
        }

    }

    // Start Call Recording
    if(RecordAllCalls || CallRecordingPolicy == "enabled") {
        StartRecording(lineObj.LineNumber);
    }

    if(includeVideo){
        // Layout for Video Call
        $("#line-"+ lineObj.LineNumber +"-progress").hide();
        $("#line-"+ lineObj.LineNumber +"-VideoCall").show();
        $("#line-"+ lineObj.LineNumber +"-ActiveCall").show();

        $("#line-"+ lineObj.LineNumber +"-btn-Conference").hide(); // Cannot conference a Video Call (Yet...)
        $("#line-"+ lineObj.LineNumber +"-btn-CancelConference").hide();
        $("#line-"+ lineObj.LineNumber +"-Conference").hide();

        $("#line-"+ lineObj.LineNumber +"-btn-Transfer").hide(); // Cannot transfer a Video Call (Yet...)
        $("#line-"+ lineObj.LineNumber +"-btn-CancelTransfer").hide();
        $("#line-"+ lineObj.LineNumber +"-Transfer").hide();

        // Default to use Camera
        $("#line-"+ lineObj.LineNumber +"-src-camera").prop("disabled", true);
        $("#line-"+ lineObj.LineNumber +"-src-canvas").prop("disabled", false);
        $("#line-"+ lineObj.LineNumber +"-src-desktop").prop("disabled", false);
        $("#line-"+ lineObj.LineNumber +"-src-video").prop("disabled", false);
    }
    else {
        // Layout for Audio Call
        $("#line-" + lineObj.LineNumber + "-progress").hide();
        $("#line-" + lineObj.LineNumber + "-VideoCall").hide();
        $("#line-" + lineObj.LineNumber + "-AudioCall").show();
        // Call Control
        $("#line-"+ lineObj.LineNumber +"-btn-Mute").show();
        $("#line-"+ lineObj.LineNumber +"-btn-Unmute").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-start-recording").show();
        $("#line-"+ lineObj.LineNumber +"-btn-stop-recording").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-Hold").show();
        $("#line-"+ lineObj.LineNumber +"-btn-Unhold").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-Transfer").show();
        $("#line-"+ lineObj.LineNumber +"-btn-CancelTransfer").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-Conference").show();
        $("#line-"+ lineObj.LineNumber +"-btn-CancelConference").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-ShowDtmf").show();
        $("#line-"+ lineObj.LineNumber +"-btn-settings").show();
        $("#line-"+ lineObj.LineNumber +"-btn-ShowCallStats").show();
        $("#line-"+ lineObj.LineNumber +"-btn-HideCallStats").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-ShowTimeline").show();
        $("#line-"+ lineObj.LineNumber +"-btn-HideTimeline").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-present-src").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-expand").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-restore").hide();
        $("#line-"+ lineObj.LineNumber +"-btn-End").show();
        // Show the Call
        $("#line-" + lineObj.LineNumber + "-ActiveCall").show();
    }

    UpdateBuddyList()
    updateLineScroll(lineObj.LineNumber);

    $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_in_progress);

    if(includeVideo && StartVideoFullScreen) ExpandVideoArea(lineObj.LineNumber);

    // Custom Web hook
    if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("accepted", session);
}
// Outgoing INVITE
function onInviteTrying(lineObj, response){
    $("#line-" + lineObj.LineNumber + "-msg").html(lang.trying);

    // Custom Web hook
    if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("trying", lineObj.SipSession);
}
function onInviteProgress(lineObj, response){
    console.log("Call Progress:", response.message.statusCode);

    // Provisional 1xx
    // response.message.reasonPhrase
    if(response.message.statusCode == 180){
        $("#line-" + lineObj.LineNumber + "-msg").html(lang.ringing);

        var soundFile = audioBlobs.EarlyMedia_European;
        if(UserLocale().indexOf("us") > -1) soundFile = audioBlobs.EarlyMedia_US;
        if(UserLocale().indexOf("gb") > -1) soundFile = audioBlobs.EarlyMedia_UK;
        if(UserLocale().indexOf("au") > -1) soundFile = audioBlobs.EarlyMedia_Australia;
        if(UserLocale().indexOf("jp") > -1) soundFile = audioBlobs.EarlyMedia_Japan;

        // Play Early Media
        console.log("Audio:", soundFile.url);
        if(lineObj.SipSession.data.earlyMedia){
            // There is already early media playing
            // onProgress can be called multiple times
            // Don't add it again
            console.log("Early Media already playing");
        }
        else {
            var earlyMedia = new Audio(soundFile.blob);
            earlyMedia.preload = "auto";
            earlyMedia.loop = true;
            earlyMedia.oncanplaythrough = function(e) {
                if (typeof earlyMedia.sinkId !== 'undefined' && getAudioOutputID() != "default") {
                    earlyMedia.setSinkId(getAudioOutputID()).then(function() {
                        console.log("Set sinkId to:", getAudioOutputID());
                    }).catch(function(e){
                        console.warn("Failed not apply setSinkId.", e);
                    });
                }
                earlyMedia.play().then(function(){
                    // Audio Is Playing
                }).catch(function(e){
                    console.warn("Unable to play audio file.", e);
                });
            }
            lineObj.SipSession.data.earlyMedia = earlyMedia;
        }
    }
    else if(response.message.statusCode === 183){
        $("#line-" + lineObj.LineNumber + "-msg").html(response.message.reasonPhrase + "...");

        // Add UI to allow DTMF
        $("#line-" + lineObj.LineNumber + "-early-dtmf").show();
    }
    else {
        // 181 = Call is Being Forwarded
        // 182 = Call is queued (Busy server!)
        // 199 = Call is Terminated (Early Dialog)

        $("#line-" + lineObj.LineNumber + "-msg").html(response.message.reasonPhrase + "...");
    }

    // Custom Web hook
    if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("progress", lineObj.SipSession);
}
function onInviteRejected(lineObj, response){
    console.log("INVITE Rejected:", response.message.reasonPhrase);

    lineObj.SipSession.data.terminateby = "them";
    lineObj.SipSession.data.reasonCode = response.message.statusCode;
    lineObj.SipSession.data.reasonText = response.message.reasonPhrase;

    teardownSession(lineObj);
}
function onInviteRedirected(response){
    console.log("onInviteRedirected", response);
    // Follow???
}

// General Session delegates
function onSessionReceivedBye(lineObj, response){
    // They Ended the call
    $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_ended);
    console.log("Call ended, bye!");

    lineObj.SipSession.data.terminateby = "them";
    lineObj.SipSession.data.reasonCode = 16;
    lineObj.SipSession.data.reasonText = "Normal Call clearing";

    response.accept(); // Send OK

    teardownSession(lineObj);
}
function onSessionReinvited(lineObj, response){
    // This may be used to include video streams
    var sdp = response.body;

    // All the possible streams will get
    // Note, this will probably happen after the streams are added
    lineObj.SipSession.data.videoChannelNames = [];
    var videoSections = sdp.split("m=video");
    if(videoSections.length >= 1){
        for(var m=0; m<videoSections.length; m++){
            if(videoSections[m].indexOf("a=mid:") > -1 && videoSections[m].indexOf("a=label:") > -1){
                // We have a label for the media
                var lines = videoSections[m].split("\r\n");
                var channel = "";
                var mid = "";
                for(var i=0; i<lines.length; i++){
                    if(lines[i].indexOf("a=label:") == 0) {
                        channel = lines[i].replace("a=label:", "");
                    }
                    if(lines[i].indexOf("a=mid:") == 0){
                        mid = lines[i].replace("a=mid:", "");
                    }
                }
                lineObj.SipSession.data.videoChannelNames.push({"mid" : mid, "channel" : channel });
            }
        }
        console.log("videoChannelNames:", lineObj.SipSession.data.videoChannelNames);
        RedrawStage(lineObj.LineNumber, false);
    }
}
function onSessionReceivedMessage(lineObj, response){
    var messageType = (response.request.headers["Content-Type"].length >=1)? response.request.headers["Content-Type"][0].parsed : "Unknown" ;
    if(messageType.indexOf("application/x-asterisk-confbridge-event") > -1){
        // Conference Events JSON
        var msgJson = JSON.parse(response.request.body);

        var session = lineObj.SipSession;
        if(!session.data.ConfbridgeChannels) session.data.ConfbridgeChannels = [];
        if(!session.data.ConfbridgeEvents) session.data.ConfbridgeEvents = [];

        if(msgJson.type == "ConfbridgeStart"){
            console.log("ConfbridgeStart!");
        }
        else if(msgJson.type == "ConfbridgeWelcome"){
            console.log("Welcome to the Asterisk Conference");
            console.log("Bridge ID:", msgJson.bridge.id);
            console.log("Bridge Name:", msgJson.bridge.name);
            console.log("Created at:", msgJson.bridge.creationtime);
            console.log("Video Mode:", msgJson.bridge.video_mode);

            session.data.ConfbridgeChannels = msgJson.channels; // Write over this
            session.data.ConfbridgeChannels.forEach(function(chan) {
                // The mute and unmute status doesn't appear to be a realtime state, only what the
                // startmuted= setting of the default profile is.
                console.log(chan.caller.name, "Is in the conference. Muted:", chan.muted, "Admin:", chan.admin);
            });
        }
        else if(msgJson.type == "ConfbridgeJoin"){
            msgJson.channels.forEach(function(chan) {
                var found = false;
                session.data.ConfbridgeChannels.forEach(function(existingChan) {
                    if(existingChan.id == chan.id) found = true;
                });
                if(!found){
                    session.data.ConfbridgeChannels.push(chan);
                    session.data.ConfbridgeEvents.push({ event: chan.caller.name + " ("+ chan.caller.number +") joined the conference", eventTime: utcDateNow() });
                    console.log(chan.caller.name, "Joined the conference. Muted: ", chan.muted);
                }
            });
        }
        else if(msgJson.type == "ConfbridgeLeave"){
            msgJson.channels.forEach(function(chan) {
                session.data.ConfbridgeChannels.forEach(function(existingChan, i) {
                    if(existingChan.id == chan.id){
                        session.data.ConfbridgeChannels.splice(i, 1);
                        console.log(chan.caller.name, "Left the conference");
                        session.data.ConfbridgeEvents.push({ event: chan.caller.name + " ("+ chan.caller.number +") left the conference", eventTime: utcDateNow() });
                    }
                });
            });
        }
        else if(msgJson.type == "ConfbridgeTalking"){
            var videoContainer = $("#line-" + lineObj.LineNumber + "-remote-videos");
            if(videoContainer){
                msgJson.channels.forEach(function(chan) {
                    videoContainer.find('video').each(function() {
                        if(this.srcObject.channel && this.srcObject.channel == chan.id) {
                            if(chan.talking_status == "on"){
                                console.log(chan.caller.name, "is talking.");
                                this.srcObject.isTalking = true;
                                $(this).css("border","1px solid red");
                            }
                            else {
                                console.log(chan.caller.name, "stopped talking.");
                                this.srcObject.isTalking = false;
                                $(this).css("border","1px solid transparent");
                            }
                        }
                    });
                });
            }
        }
        else if(msgJson.type == "ConfbridgeMute"){
            msgJson.channels.forEach(function(chan) {
                session.data.ConfbridgeChannels.forEach(function(existingChan) {
                    if(existingChan.id == chan.id){
                        console.log(existingChan.caller.name, "is now muted");
                        existingChan.muted = true;
                    }
                });
            });
            RedrawStage(lineObj.LineNumber, false);
        }
        else if(msgJson.type == "ConfbridgeUnmute"){
            msgJson.channels.forEach(function(chan) {
                session.data.ConfbridgeChannels.forEach(function(existingChan) {
                    if(existingChan.id == chan.id){
                        console.log(existingChan.caller.name, "is now unmuted");
                        existingChan.muted = false;
                    }
                });
            });
            RedrawStage(lineObj.LineNumber, false);
        }
        else if(msgJson.type == "ConfbridgeEnd"){
            console.log("The Asterisk Conference has ended, bye!");
        }
        else {
            console.warn("Unknown Asterisk Conference Event:", msgJson.type, msgJson);
        }
        RefreshLineActivity(lineObj.LineNumber);
        response.accept();
    }
    else if(messageType.indexOf("application/x-myphone-confbridge-chat") > -1){
        console.log("x-myphone-confbridge-chat", response);


        response.accept();
    }
    else {
        console.warn("Unknown message type")
        response.reject();
    }
}

function onSessionDescriptionHandlerCreated(lineObj, sdh, provisional, includeVideo){
    if (sdh) {
        if(sdh.peerConnection){
            // console.log(sdh);
            sdh.peerConnection.ontrack = function(event){
                // console.log(event);
                onTrackAddedEvent(lineObj, includeVideo);
            }
            // sdh.peerConnectionDelegate = {
            //     ontrack: function(event){
            //         console.log(event);
            //         onTrackAddedEvent(lineObj, includeVideo);
            //     }
            // }
        }
        else{
            console.warn("onSessionDescriptionHandler fired without a peerConnection");
        }
    }
    else{
        console.warn("onSessionDescriptionHandler fired without a sessionDescriptionHandler");
    }
}
function onTrackAddedEvent(lineObj, includeVideo){
    // Gets remote tracks
    var session = lineObj.SipSession;
    // TODO: look at detecting video, so that UI switches to audio/video automatically.

    var pc = session.sessionDescriptionHandler.peerConnection;

    var remoteAudioStream = new MediaStream();
    var remoteVideoStream = new MediaStream();

    pc.getTransceivers().forEach(function (transceiver) {
        // Add Media
        var receiver = transceiver.receiver;
        if(receiver.track){
            if(receiver.track.kind == "audio"){
                console.log("Adding Remote Audio Track");
                remoteAudioStream.addTrack(receiver.track);
            }
            if(includeVideo && receiver.track.kind == "video"){
                if(transceiver.mid){
                    receiver.track.mid = transceiver.mid;
                    console.log("Adding Remote Video Track - ", receiver.track.readyState , "MID:", receiver.track.mid);
                    remoteVideoStream.addTrack(receiver.track);
                }
            }
        }
    });

    // Attach Audio
    if(remoteAudioStream.getAudioTracks().length >= 1){
        var remoteAudio = $("#line-" + lineObj.LineNumber + "-remoteAudio").get(0);
        remoteAudio.srcObject = remoteAudioStream;
        remoteAudio.onloadedmetadata = function(e) {
            if (typeof remoteAudio.sinkId !== 'undefined') {
                remoteAudio.setSinkId(getAudioOutputID()).then(function(){
                    console.log("sinkId applied: "+ getAudioOutputID());
                }).catch(function(e){
                    console.warn("Error using setSinkId: ", e);
                });
            }
            remoteAudio.play();
        }
    }

    if(includeVideo){
        // Single Or Multiple View
        $("#line-" + lineObj.LineNumber + "-remote-videos").empty();
        if(remoteVideoStream.getVideoTracks().length >= 1){
            var remoteVideoStreamTracks = remoteVideoStream.getVideoTracks();
            remoteVideoStreamTracks.forEach(function(remoteVideoStreamTrack) {
                var thisRemoteVideoStream = new MediaStream();
                thisRemoteVideoStream.trackID = remoteVideoStreamTrack.id;
                thisRemoteVideoStream.mid = remoteVideoStreamTrack.mid;
                remoteVideoStreamTrack.onended = function() {
                    console.log("Video Track Ended: ", this.mid);
                    RedrawStage(lineObj.LineNumber, true);
                }
                thisRemoteVideoStream.addTrack(remoteVideoStreamTrack);

                var wrapper = $("<span />", {
                    class: "VideoWrapper",
                });
                wrapper.css("width", "1px");
                wrapper.css("heigh", "1px");
                wrapper.hide();

                var callerID = $("<div />", {
                    class: "callerID"
                });
                wrapper.append(callerID);

                var Actions = $("<div />", {
                    class: "Actions"
                });
                wrapper.append(Actions);

                var videoEl = $("<video />", {
                    id: remoteVideoStreamTrack.id,
                    mid: remoteVideoStreamTrack.mid,
                    muted: true,
                    autoplay: true,
                    playsinline: true,
                    controls: false
                });
                videoEl.hide();

                var videoObj = videoEl.get(0);
                videoObj.srcObject = thisRemoteVideoStream;
                videoObj.onloadedmetadata = function(e) {
                    // videoObj.play();
                    videoEl.show();
                    videoEl.parent().show();
                    console.log("Playing Video Stream MID:", thisRemoteVideoStream.mid);
                    RedrawStage(lineObj.LineNumber, true);
                }
                wrapper.append(videoEl);

                $("#line-" + lineObj.LineNumber + "-remote-videos").append(wrapper);

                console.log("Added Video Element MID:", thisRemoteVideoStream.mid);
            });
        }
        else {
            console.log("No Video Streams");
            RedrawStage(lineObj.LineNumber, true);
        }
    }

    // Custom Web hook
    if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("trackAdded", session);
}

// General end of Session
function teardownSession(lineObj) {
    if(lineObj == null || lineObj.SipSession == null) return;

    var session = lineObj.SipSession;
    if(session.data.teardownComplete == true) return;
    session.data.teardownComplete = true; // Run this code only once

    // Call UI
    if(session.data.earlyReject != true){
        HidePopup();
    }

    // End any child calls
    if(session.data.childsession){
        session.data.childsession.dispose().then(function(){
            session.data.childsession = null;
        }).catch(function(error){
            session.data.childsession = null;
            // Suppress message
        });
    }

    // Mixed Tracks
    if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
        session.data.AudioSourceTrack.stop();
        session.data.AudioSourceTrack = null;
    }
    // Stop any Early Media
    if(session.data.earlyMedia){
        session.data.earlyMedia.pause();
        session.data.earlyMedia.removeAttribute('src');
        session.data.earlyMedia.load();
        session.data.earlyMedia = null;
    }
    // Stop any ringing calls
    if(session.data.ringerObj){
        session.data.ringerObj.pause();
        session.data.ringerObj.removeAttribute('src');
        session.data.ringerObj.load();
        session.data.ringerObj = null;
    }

    // Stop Recording if we are
    StopRecording(lineObj.LineNumber,true);

    // Audio Meters
    if(lineObj.LocalSoundMeter != null){
        lineObj.LocalSoundMeter.stop();
        lineObj.LocalSoundMeter = null;
    }
    if(lineObj.RemoteSoundMeter != null){
        lineObj.RemoteSoundMeter.stop();
        lineObj.RemoteSoundMeter = null;
    }

    // Make sure you have released the microphone
    if(session && session.sessionDescriptionHandler && session.sessionDescriptionHandler.peerConnection){
        var pc = session.sessionDescriptionHandler.peerConnection;
        pc.getSenders().forEach(function (RTCRtpSender) {
            if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                RTCRtpSender.track.stop();
            }
        });
    }

    // End timers
    window.clearInterval(session.data.videoResampleInterval);
    window.clearInterval(session.data.callTimer);

    // Add to stream
    AddCallMessage(lineObj.BuddyObj.identity, session);

    // Check if this call was missed
    if (session.data.calldirection == "inbound"){
        if(session.data.earlyReject){
            // Call was rejected without even ringing
            IncreaseMissedBadge(session.data.buddyId);
        } else if (session.data.terminateby == "them" && session.data.startTime == null){
            // Call Terminated by them during ringing
            if(session.data.reasonCode == 0){
                // Call was canceled, and not answered elsewhere
                IncreaseMissedBadge(session.data.buddyId);
            }
        }
    }

    // Close up the UI
    window.setTimeout(function () {
        RemoveLine(lineObj);
    }, 1000);

    UpdateBuddyList();
    if(session.data.earlyReject != true){
        UpdateUI();
    }

    // Custom Web hook
    if(typeof web_hook_on_terminate !== 'undefined') web_hook_on_terminate(session);
}



// Sounds Meter Class
// ==================
class SoundMeter {
    constructor(sessionId, lineNum) {
        var audioContext = null;
        try {
            window.AudioContext = window.AudioContext || window.webkitAudioContext;
            audioContext = new AudioContext();
        }
        catch(e) {
            console.warn("AudioContext() LocalAudio not available... its fine.");
        }
        if (audioContext == null) return null;
        this.context = audioContext;
        this.source = null;

        this.lineNum = lineNum;
        this.sessionId = sessionId;

        this.captureInterval = null;
        this.levelsInterval = null;
        this.networkInterval = null;
        this.startTime = 0;

        this.ReceiveBitRateChart = null;
        this.ReceiveBitRate = [];
        this.ReceivePacketRateChart = null;
        this.ReceivePacketRate = [];
        this.ReceivePacketLossChart = null;
        this.ReceivePacketLoss = [];
        this.ReceiveJitterChart = null;
        this.ReceiveJitter = [];
        this.ReceiveLevelsChart = null;
        this.ReceiveLevels = [];
        this.SendBitRateChart = null;
        this.SendBitRate = [];
        this.SendPacketRateChart = null;
        this.SendPacketRate = [];

        this.instant = 0; // Primary Output indicator

        this.AnalyserNode = this.context.createAnalyser();
        this.AnalyserNode.minDecibels = -90;
        this.AnalyserNode.maxDecibels = -10;
        this.AnalyserNode.smoothingTimeConstant = 0.85;
    }
    connectToSource(stream, callback) {
        console.log("SoundMeter connecting...");
        try {
            this.source = this.context.createMediaStreamSource(stream);
            this.source.connect(this.AnalyserNode);
            // this.AnalyserNode.connect(this.context.destination); // Can be left unconnected
            this._start();

            callback(null);
        }
        catch(e) {
            console.error(e); // Probably not audio track
            callback(e);
        }
    }
    _start(){
        var self = this;
        self.instant = 0;
        self.AnalyserNode.fftSize = 32; // 32, 64, 128, 256, 512, 1024, 2048, 4096, 8192, 16384, and 32768. Defaults to 2048
        self.dataArray = new Uint8Array(self.AnalyserNode.frequencyBinCount);

        this.captureInterval = window.setInterval(function(){
            self.AnalyserNode.getByteFrequencyData(self.dataArray); // Populate array with data from 0-255

            // Just take the maximum value of this data
            self.instant = 0;
            for(var d = 0; d < self.dataArray.length; d++) {
                if(self.dataArray[d] > self.instant) self.instant = self.dataArray[d];
            }

        }, 1);
    }
    stop() {
        console.log("Disconnecting SoundMeter...");
        window.clearInterval(this.captureInterval);
        this.captureInterval = null;
        window.clearInterval(this.levelsInterval);
        this.levelsInterval = null;
        window.clearInterval(this.networkInterval);
        this.networkInterval = null;
        try {
            this.source.disconnect();
        }
        catch(e) { }
        this.source = null;
        try {
            this.AnalyserNode.disconnect();
        }
        catch(e) { }
        this.AnalyserNode = null;
        try {
            this.context.close();
        }
        catch(e) { }
        this.context = null;

        // Save to IndexDb
        var lineObj = FindLineByNumber(this.lineNum);
        var QosData = {
            ReceiveBitRate: this.ReceiveBitRate,
            ReceivePacketRate: this.ReceivePacketRate,
            ReceivePacketLoss: this.ReceivePacketLoss,
            ReceiveJitter: this.ReceiveJitter,
            ReceiveLevels: this.ReceiveLevels,
            SendBitRate: this.SendBitRate,
            SendPacketRate: this.SendPacketRate,
        }
        if(this.sessionId != null){
            SaveQosData(QosData, this.sessionId, lineObj.BuddyObj.identity);
        }
    }
}
function MeterSettingsOutput(audioStream, objectId, direction, interval){
    var soundMeter = new SoundMeter(null, null);
    soundMeter.startTime = Date.now();
    soundMeter.connectToSource(audioStream, function (e) {
        if (e != null) return;

        console.log("SoundMeter Connected, displaying levels to:"+ objectId);
        soundMeter.levelsInterval = window.setInterval(function () {
            // Calculate Levels (0 - 255)
            var instPercent = (soundMeter.instant/255) * 100;
            $("#"+ objectId).css(direction, instPercent.toFixed(2) +"%");
        }, interval);
    });

    return soundMeter;
}

// QOS
// ===
function SaveQosData(QosData, sessionId, buddy){
    var indexedDB = window.indexedDB;
    var request = indexedDB.open("CallQosData", 1);
    request.onerror = function(event) {
        console.error("IndexDB Request Error:", event);
    }
    request.onupgradeneeded = function(event) {
        console.warn("Upgrade Required for IndexDB... probably because of first time use.");
        var IDB = event.target.result;

        // Create Object Store
        if(IDB.objectStoreNames.contains("CallQos") == false){
            var objectStore = IDB.createObjectStore("CallQos", { keyPath: "uID" });
            objectStore.createIndex("sessionid", "sessionid", { unique: false });
            objectStore.createIndex("buddy", "buddy", { unique: false });
            objectStore.createIndex("QosData", "QosData", { unique: false });
        }
        else {
            console.warn("IndexDB requested upgrade, but object store was in place");
        }
    }
    request.onsuccess = function(event) {
        console.log("IndexDB connected to CallQosData");

        var IDB = event.target.result;
        if(IDB.objectStoreNames.contains("CallQos") == false){
            console.warn("IndexDB CallQosData.CallQos does not exists");
            IDB.close();
            window.indexedDB.deleteDatabase("CallQosData"); // This should help if the table structure has not been created.
            return;
        }
        IDB.onerror = function(event) {
            console.error("IndexDB Error:", event);
        }

        // Prepare data to write
        var data = {
            uID: uID(),
            sessionid: sessionId,
            buddy: buddy,
            QosData: QosData
        }
        // Commit Transaction
        var transaction = IDB.transaction(["CallQos"], "readwrite");
        var objectStoreAdd = transaction.objectStore("CallQos").add(data);
        objectStoreAdd.onsuccess = function(event) {
            console.log("Call CallQos Success: ", sessionId);
        }
    }
}
function DisplayQosData(sessionId){
    var indexedDB = window.indexedDB;
    var request = indexedDB.open("CallQosData", 1);
    request.onerror = function(event) {
        console.error("IndexDB Request Error:", event);
    }
    request.onupgradeneeded = function(event) {
        console.warn("Upgrade Required for IndexDB... probably because of first time use.");
    }
    request.onsuccess = function(event) {
        console.log("IndexDB connected to CallQosData");

        var IDB = event.target.result;
        if(IDB.objectStoreNames.contains("CallQos") == false){
            console.warn("IndexDB CallQosData.CallQos does not exists");
            return;
        }

        var transaction = IDB.transaction(["CallQos"]);
        var objectStoreGet = transaction.objectStore("CallQos").index('sessionid').getAll(sessionId);
        objectStoreGet.onerror = function(event) {
            console.error("IndexDB Get Error:", event);
        }
        objectStoreGet.onsuccess = function(event) {
            if(event.target.result && event.target.result.length == 2){
                // This is the correct data

                var QosData0 = event.target.result[0].QosData;
                // ReceiveBitRate: (8) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]
                // ReceiveJitter: (8) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]
                // ReceiveLevels: (9) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]
                // ReceivePacketLoss: (8) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]
                // ReceivePacketRate: (8) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]
                // SendBitRate: []
                // SendPacketRate: []
                var QosData1 = event.target.result[1].QosData;
                // ReceiveBitRate: []
                // ReceiveJitter: []
                // ReceiveLevels: []
                // ReceivePacketLoss: []
                // ReceivePacketRate: []
                // SendBitRate: (9) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]
                // SendPacketRate: (9) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]

                Chart.defaults.global.defaultFontSize = 12;

                var ChatHistoryOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true } //, min: 0, max: 100
                        }],
                        xAxes: [{
                            display: false
                        }]
                    },
                }


                // ReceiveBitRateChart
                var labelSet = [];
                var dataset = [];
                var data = (QosData0.ReceiveBitRate.length > 0)? QosData0.ReceiveBitRate : QosData1.ReceiveBitRate;
                $.each(data, function(i,item){
                    labelSet.push(moment.utc(item.timestamp.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat));
                    dataset.push(item.value);
                });
                var ReceiveBitRateChart = new Chart($("#cdr-AudioReceiveBitRate"), {
                    type: 'line',
                    data: {
                        labels: labelSet,
                        datasets: [{
                            label: lang.receive_kilobits_per_second,
                            data: dataset,
                            backgroundColor: 'rgba(168, 0, 0, 0.5)',
                            borderColor: 'rgba(168, 0, 0, 1)',
                            borderWidth: 1,
                            pointRadius: 1
                        }]
                    },
                    options: ChatHistoryOptions
                });

                // ReceivePacketRateChart
                var labelSet = [];
                var dataset = [];
                var data = (QosData0.ReceivePacketRate.length > 0)? QosData0.ReceivePacketRate : QosData1.ReceivePacketRate;
                $.each(data, function(i,item){
                    labelSet.push(moment.utc(item.timestamp.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat));
                    dataset.push(item.value);
                });
                var ReceivePacketRateChart = new Chart($("#cdr-AudioReceivePacketRate"), {
                    type: 'line',
                    data: {
                        labels: labelSet,
                        datasets: [{
                            label: lang.receive_packets_per_second,
                            data: dataset,
                            backgroundColor: 'rgba(168, 0, 0, 0.5)',
                            borderColor: 'rgba(168, 0, 0, 1)',
                            borderWidth: 1,
                            pointRadius: 1
                        }]
                    },
                    options: ChatHistoryOptions
                });

                // AudioReceivePacketLossChart
                var labelSet = [];
                var dataset = [];
                var data = (QosData0.ReceivePacketLoss.length > 0)? QosData0.ReceivePacketLoss : QosData1.ReceivePacketLoss;
                $.each(data, function(i,item){
                    labelSet.push(moment.utc(item.timestamp.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat));
                    dataset.push(item.value);
                });
                var AudioReceivePacketLossChart = new Chart($("#cdr-AudioReceivePacketLoss"), {
                    type: 'line',
                    data: {
                        labels: labelSet,
                        datasets: [{
                            label: lang.receive_packet_loss,
                            data: dataset,
                            backgroundColor: 'rgba(168, 99, 0, 0.5)',
                            borderColor: 'rgba(168, 99, 0, 1)',
                            borderWidth: 1,
                            pointRadius: 1
                        }]
                    },
                    options: ChatHistoryOptions
                });

                // AudioReceiveJitterChart
                var labelSet = [];
                var dataset = [];
                var data = (QosData0.ReceiveJitter.length > 0)? QosData0.ReceiveJitter : QosData1.ReceiveJitter;
                $.each(data, function(i,item){
                    labelSet.push(moment.utc(item.timestamp.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat));
                    dataset.push(item.value);
                });
                var AudioReceiveJitterChart = new Chart($("#cdr-AudioReceiveJitter"), {
                    type: 'line',
                    data: {
                        labels: labelSet,
                        datasets: [{
                            label: lang.receive_jitter,
                            data: dataset,
                            backgroundColor: 'rgba(0, 38, 168, 0.5)',
                            borderColor: 'rgba(0, 38, 168, 1)',
                            borderWidth: 1,
                            pointRadius: 1
                        }]
                    },
                    options: ChatHistoryOptions
                });

                // AudioReceiveLevelsChart
                var labelSet = [];
                var dataset = [];
                var data = (QosData0.ReceiveLevels.length > 0)? QosData0.ReceiveLevels : QosData1.ReceiveLevels;
                $.each(data, function(i,item){
                    labelSet.push(moment.utc(item.timestamp.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat));
                    dataset.push(item.value);
                });
                var AudioReceiveLevelsChart = new Chart($("#cdr-AudioReceiveLevels"), {
                    type: 'line',
                    data: {
                        labels: labelSet,
                        datasets: [{
                            label: lang.receive_audio_levels,
                            data: dataset,
                            backgroundColor: 'rgba(140, 0, 168, 0.5)',
                            borderColor: 'rgba(140, 0, 168, 1)',
                            borderWidth: 1,
                            pointRadius: 1
                        }]
                    },
                    options: ChatHistoryOptions
                });

                // SendPacketRateChart
                var labelSet = [];
                var dataset = [];
                var data = (QosData0.SendPacketRate.length > 0)? QosData0.SendPacketRate : QosData1.SendPacketRate;
                $.each(data, function(i,item){
                    labelSet.push(moment.utc(item.timestamp.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat));
                    dataset.push(item.value);
                });
                var SendPacketRateChart = new Chart($("#cdr-AudioSendPacketRate"), {
                    type: 'line',
                    data: {
                        labels: labelSet,
                        datasets: [{
                            label: lang.send_packets_per_second,
                            data: dataset,
                            backgroundColor: 'rgba(0, 121, 19, 0.5)',
                            borderColor: 'rgba(0, 121, 19, 1)',
                            borderWidth: 1,
                            pointRadius: 1
                        }]
                    },
                    options: ChatHistoryOptions
                });

                // AudioSendBitRateChart
                var labelSet = [];
                var dataset = [];
                var data = (QosData0.SendBitRate.length > 0)? QosData0.SendBitRate : QosData1.SendBitRate;
                $.each(data, function(i,item){
                    labelSet.push(moment.utc(item.timestamp.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat));
                    dataset.push(item.value);
                });
                var AudioSendBitRateChart = new Chart($("#cdr-AudioSendBitRate"), {
                    type: 'line',
                    data: {
                        labels: labelSet,
                        datasets: [{
                            label: lang.send_kilobits_per_second,
                            data: dataset,
                            backgroundColor: 'rgba(0, 121, 19, 0.5)',
                            borderColor: 'rgba(0, 121, 19, 1)',
                            borderWidth: 1,
                            pointRadius: 1
                        }]
                    },
                    options: ChatHistoryOptions
                });

            } else{
                console.warn("Result not expected", event.target.result);
            }
        }
    }
}
function DeleteQosData(buddy, stream){
    var indexedDB = window.indexedDB;
    var request = indexedDB.open("CallQosData", 1);
    request.onerror = function(event) {
        console.error("IndexDB Request Error:", event);
    }
    request.onupgradeneeded = function(event) {
        console.warn("Upgrade Required for IndexDB... probably because of first time use.");
        // If this is the case, there will be no call recordings
    }
    request.onsuccess = function(event) {
        console.log("IndexDB connected to CallQosData");

        var IDB = event.target.result;
        if(IDB.objectStoreNames.contains("CallQos") == false){
            console.warn("IndexDB CallQosData.CallQos does not exists");
            return;
        }
        IDB.onerror = function(event) {
            console.error("IndexDB Error:", event);
        }

        // Loop and Delete
        // Note:  This database can only delete based on Primary Key
        // The The Primary Key is arbitrary, so you must get all the rows based
        // on a lookup, and delete from there.
        $.each(stream.DataCollection, function (i, item) {
            if (item.ItemType == "CDR" && item.SessionId && item.SessionId != "") {
                console.log("Deleting CallQosData: ", item.SessionId);
                var objectStore = IDB.transaction(["CallQos"], "readwrite").objectStore("CallQos");
                var objectStoreGet = objectStore.index('sessionid').getAll(item.SessionId);
                objectStoreGet.onerror = function(event) {
                    console.error("IndexDB Get Error:", event);
                }
                objectStoreGet.onsuccess = function(event) {
                    if(event.target.result && event.target.result.length > 0){
                        // There sre some rows to delete
                        $.each(event.target.result, function(i, item){
                            // console.log("Delete: ", item.uID);
                            try{
                                objectStore.delete(item.uID);
                            } catch(e){
                                console.log("Call CallQosData Delete failed: ", e);
                            }
                        });
                    }
                }
            }
        });


    }
}

// Presence / Subscribe
// ====================
function SubscribeAll() {
    if(!userAgent.isRegistered()) return;

    if(VoiceMailSubscribe){
        SubscribeVoicemail();
    }
    if(SubscribeToYourself){
        SelfSubscribe();
    }

    // Start subscribe all
    if(userAgent.BlfSubs && userAgent.BlfSubs.length > 0){
        UnsubscribeAll();
    }
    userAgent.BlfSubs = [];
    if(Buddies.length >= 1){
        console.log("Starting Subscribe of all ("+ Buddies.length +") Extension Buddies...");
        for(var b=0; b<Buddies.length; b++) {
            SubscribeBuddy(Buddies[b]);
        }
    }
}
function SelfSubscribe(){
    if(!userAgent.isRegistered()) return;

    if(userAgent.selfSub){
        console.log("Unsubscribe from old self subscribe...");
        SelfUnsubscribe();
    }

    var targetURI = SIP.UserAgent.makeURI("sip:" + SipUsername + "@" + SipDomain);

    var options = {
        expires: SubscribeBuddyExpires,
        extraHeaders: ['Accept: '+ SubscribeBuddyAccept]
    }

    userAgent.selfSub = new SIP.Subscriber(userAgent, targetURI, SubscribeBuddyEvent, options);
    userAgent.selfSub.delegate = {
        onNotify: function(sip) {
            ReceiveNotify(sip, true);
        }
    }
    console.log("SUBSCRIBE Self: "+ SipUsername +"@" + SipDomain);
    userAgent.selfSub.subscribe().catch(function(error){
        console.warn("Error subscribing to yourself:", error);
    });
}

function SubscribeVoicemail(){
    if(!userAgent.isRegistered()) return;

    if(userAgent.voicemailSub){
        console.log("Unsubscribe from old voicemail Messages...");
        UnsubscribeVoicemail();
    }

    var vmOptions = { expires : SubscribeVoicemailExpires }
    var targetURI = SIP.UserAgent.makeURI("sip:" + SipUsername + "@" + SipDomain);
    userAgent.voicemailSub = new SIP.Subscriber(userAgent, targetURI, "message-summary", vmOptions);
    userAgent.voicemailSub.delegate = {
        onNotify: function(sip) {
            VoicemailNotify(sip);
        }
    }
    console.log("SUBSCRIBE VOICEMAIL: "+ SipUsername +"@" + SipDomain);
    userAgent.voicemailSub.subscribe().catch(function(error){
        console.warn("Error subscribing to voicemail notifications:", error);
    });
}


function SubscribeBuddy(buddyObj) {
    if(!userAgent.isRegistered()) return;

    if((buddyObj.type == "extension" || buddyObj.type == "xmpp") && buddyObj.EnableSubscribe == true && buddyObj.SubscribeUser != "") {

        var targetURI = SIP.UserAgent.makeURI("sip:" + buddyObj.SubscribeUser + "@" + SipDomain);

        var options = {
            expires: SubscribeBuddyExpires,
            extraHeaders: ['Accept: '+ SubscribeBuddyAccept]
        }
        var blfSubscribe = new SIP.Subscriber(userAgent, targetURI, SubscribeBuddyEvent, options);
        blfSubscribe.data = {}
        blfSubscribe.data.buddyId = buddyObj.identity;
        blfSubscribe.delegate = {
            onNotify: function(sip) {
                ReceiveNotify(sip, false);
            }
        }
        console.log("SUBSCRIBE: "+ buddyObj.SubscribeUser +"@" + SipDomain);
        blfSubscribe.subscribe().catch(function(error){
            console.warn("Error subscribing to Buddy notifications:", error);
        });

        if(!userAgent.BlfSubs) userAgent.BlfSubs = [];
        userAgent.BlfSubs.push(blfSubscribe);
    }
}

function UnsubscribeAll() {
    if(!userAgent.isRegistered()) return;

    console.log("Unsubscribe from voicemail Messages...");
    UnsubscribeVoicemail();

    if(userAgent.BlfSubs && userAgent.BlfSubs.length > 0){
        console.log("Unsubscribing "+ userAgent.BlfSubs.length + " subscriptions...");
        for (var blf = 0; blf < userAgent.BlfSubs.length; blf++) {
            UnsubscribeBlf(userAgent.BlfSubs[blf]);
        }
        userAgent.BlfSubs = [];

        for(var b=0; b<Buddies.length; b++) {
            var buddyObj = Buddies[b];
            if(buddyObj.type == "extension" || buddyObj.type == "xmpp") {
                $("#contact-" + buddyObj.identity + "-devstate").prop("class", "dotOffline");
                $("#contact-" + buddyObj.identity + "-devstate-main").prop("class", "dotOffline");
                $("#contact-" + buddyObj.identity + "-presence").html(lang.state_unknown);
                $("#contact-" + buddyObj.identity + "-presence-main").html(lang.state_unknown);
            }
        }
    }
}
function UnsubscribeBlf(blfSubscribe){
    if(!userAgent.isRegistered()) return;

    if(blfSubscribe.state == SIP.SubscriptionState.Subscribed){
        console.log("Unsubscribe to BLF Messages...", blfSubscribe.data.buddyId);
        blfSubscribe.unsubscribe().catch(function(error){
            console.warn("Error removing BLF notifications:", error);
        });
    }
    else {
        console.log("Incorrect buddy subscribe state", blfSubscribe.data.buddyId, blfSubscribe.state);
    }
    blfSubscribe.dispose().catch(function(error){
        console.warn("Error disposing BLF notifications:", error);
    });
    blfSubscribe = null;
}
function UnsubscribeVoicemail(){
    if(!userAgent.isRegistered()) return;

    if(userAgent.voicemailSub){
        console.log("Unsubscribe to voicemail Messages...", userAgent.voicemailSub.state);
        if(userAgent.voicemailSub.state == SIP.SubscriptionState.Subscribed){
            userAgent.voicemailSub.unsubscribe().catch(function(error){
                console.warn("Error removing voicemail notifications:", error);
            });
        }
        userAgent.voicemailSub.dispose().catch(function(error){
            console.warn("Error disposing voicemail notifications:", error);
        });
    } else {
        console.log("Not subscribed to MWI");
    }
    userAgent.voicemailSub = null;
}
function SelfUnsubscribe(){
    if(!userAgent.isRegistered()) return;

    if(userAgent.selfSub){
        console.log("Unsubscribe from yourself...", userAgent.selfSub.state);
        if(userAgent.selfSub.state == SIP.SubscriptionState.Subscribed){
            userAgent.selfSub.unsubscribe().catch(function(error){
                console.warn("Error self subscription:", error);
            });
        }
        userAgent.selfSub.dispose().catch(function(error){
            console.warn("Error disposing self subscription:", error);
        });
    } else {
        console.log("Not subscribed to Yourself");
    }
    userAgent.selfSub = null;
}

function UnsubscribeBuddy(buddyObj) {
    console.log("Unsubscribe: ", buddyObj.identity);
    if(buddyObj.type == "extension" || buddyObj.type == "xmpp") {
        if(userAgent && userAgent.BlfSubs && userAgent.BlfSubs.length > 0){
            for (var blf = 0; blf < userAgent.BlfSubs.length; blf++) {
                var blfSubscribe = userAgent.BlfSubs[blf];
                if(blfSubscribe.data.buddyId == buddyObj.identity){
                    console.log("Subscription found, removing: ", buddyObj.identity);
                    UnsubscribeBlf(userAgent.BlfSubs[blf]);
                    userAgent.BlfSubs.splice(blf, 1);
                    break;
                }
            }
        }
    }
}
// Subscription Events
// ===================
function VoicemailNotify(notification){
    // Messages-Waiting: yes        <-- yes/no
    // Voice-Message: 1/0           <-- new/old
    // Voice-Message: 1/0 (0/0)     <-- new/old (ugent new/old)
    if(notification.request.body.indexOf("Messages-Waiting:") > -1){
        notification.accept();

        var messagesWaiting = (notification.request.body.indexOf("Messages-Waiting: yes") > -1)
        var newVoiceMessages = 0;
        var oldVoiceMessages = 0;
        var ugentNewVoiceMessage = 0;
        var ugentOldVoiceMessage = 0;

        if(messagesWaiting){
            console.log("Messages Waiting!");
            var lines = notification.request.body.split("\r\n");
            for(var l=0; l<lines.length; l++){
                if(lines[l].indexOf("Voice-Message: ") > -1){
                    var value = lines[l].replace("Voice-Message: ", ""); // 1/0 (0/0)
                    if(value.indexOf(" (") > -1){
                        // With Ugent options
                        newVoiceMessages = parseInt(value.split(" (")[0].split("\/")[0]);
                        oldVoiceMessages = parseInt(value.split(" (")[0].split("\/")[1]);
                        ugentNewVoiceMessage = parseInt(value.split(" (")[1].replace(")","").split("\/")[0]);
                        ugentOldVoiceMessage = parseInt(value.split(" (")[1].replace(")","").split("\/")[1]);
                    } else {
                        // Without
                        newVoiceMessages = parseInt(value.split("\/")[0]);
                        oldVoiceMessages = parseInt(value.split("\/")[1]);
                    }
                }
            }
            console.log("Voicemail: ", newVoiceMessages, oldVoiceMessages, ugentNewVoiceMessage, ugentOldVoiceMessage);

            // Show the messages waiting bubble
            $("#TxtVoiceMessages").html(""+ newVoiceMessages)
            $("#TxtVoiceMessages").show();

            // Show a system notification
            if(newVoiceMessages > userAgent.lastVoicemailCount){
                userAgent.lastVoicemailCount = newVoiceMessages;

                if ("Notification" in window) {
                    if (Notification.permission === "granted") {

                        var noticeOptions = {
                            body: lang.you_have_new_voice_mail.replace("{0}", newVoiceMessages)
                        }

                        var vmNotification = new Notification(lang.new_voice_mail, noticeOptions);
                        vmNotification.onclick = function (event) {
                            if(VoicemailDid != ""){
                                DialByLine("audio", null, VoicemailDid, lang.voice_mail);
                            }
                        }
                    }
                }

            }

        } else {
            // Hide the messages waiting bubble
            $("#TxtVoiceMessages").html("0")
            $("#TxtVoiceMessages").hide();
        }

        if(typeof web_hook_on_messages_waiting !== 'undefined') {
            web_hook_on_messages_waiting(newVoiceMessages, oldVoiceMessages, ugentNewVoiceMessage, ugentOldVoiceMessage);
        }
    }
    else {
        // Doesn't seem to be an message notification https://datatracker.ietf.org/doc/html/rfc3842
        notification.reject();
    }
}
function ReceiveNotify(notification, selfSubscribe) {
    if (userAgent == null || !userAgent.isRegistered()) return;

    notification.accept();

    var buddy = "";
    var dotClass = "dotOffline";
    var Presence = "Unknown";

    var ContentType = notification.request.headers["Content-Type"][0].parsed;
    if (ContentType == "application/pidf+xml") {
        // Handle Presence
        /*
        // Asterisk chan_sip
        <?xml version="1.0" encoding="ISO-8859-1"?>
        <presence
            xmlns="urn:ietf:params:xml:ns:pidf"
            xmlns:pp="urn:ietf:params:xml:ns:pidf:person"
            xmlns:es="urn:ietf:params:xml:ns:pidf:rid:status:rid-status"
            xmlns:ep="urn:ietf:params:xml:ns:pidf:rid:rid-person"
            entity="sip:webrtc@192.168.88.98">

            <pp:person>
                <status>
                    <ep:activities>
                        <ep:away/>
                    </ep:activities>
                </status>
            </pp:person>

            <note>Not online</note>
            <tuple id="300">
                <contact priority="1">sip:300@192.168.88.98</contact>
                <status>
                    <basic>open | closed</basic>
                </status>
            </tuple>
        </presence>

        // Asterisk chan_pj-sip
        <?xml version="1.0" encoding="UTF-8"?>
        <presence
            entity="sip:300@192.168.88.40:443;transport=ws"
            xmlns="urn:ietf:params:xml:ns:pidf"
            xmlns:dm="urn:ietf:params:xml:ns:pidf:data-model"
            xmlns:rid="urn:ietf:params:xml:ns:pidf:rid">
            <note>Ready</note>
            <tuple id="300">
                <status>
                    <basic>open</basic>
                </status>
                <contact priority="1">sip:User1@raspberrypi.local</contact>
            </tuple>
            <dm:person />
        </presence>

        // OpenSIPS
        <?xml version="1.0"?>
        <presence
            xmlns="urn:ietf:params:xml:ns:pidf"
            entity="sip:200@ws-eu-west-1.innovateasterisk.com">
            <tuple xmlns="urn:ietf:params:xml:ns:pidf" id="tuple_mixing-id">
                <status>
                    <basic>closed</basic>
                </status>
            </tuple>
        </presence>

        <?xml version="1.0"?>
        <presence
            xmlns="urn:ietf:params:xml:ns:pidf"
            entity="sip:TTbXG7XMO@ws-eu-west-1.innovateasterisk.com">
            <tuple
                xmlns="urn:ietf:params:xml:ns:pidf"
                id="0x7ffe17f496c0">
                <status>
                    <basic>open</basic>
                </status>
            </tuple>
        </presence>


        <?xml version="1.0"?>
        <presence
            xmlns="urn:ietf:params:xml:ns:pidf"
            entity="sip:TTbXG7XMO@ws-eu-west-1.innovateasterisk.com">
            <tuple
                xmlns="urn:ietf:params:xml:ns:pidf"
                id="tuple_mixing-id">
                <status>
                    <basic>open</basic>
                </status>
            </tuple>
            <note xmlns="urn:ietf:params:xml:ns:pidf">On the phone</note>
            <dm:person
                xmlns:dm="urn:ietf:params:xml:ns:pidf:data-model"
                xmlns:rid="urn:ietf:params:xml:ns:pidf:rid"
                id="peers_mixing-id">
                <rid:activities>
                    <rid:on-the-phone/>
                </rid:activities>
                <dm:note>On the phone</dm:note>
            </dm:person>
        </presence>

        // There can be more than one tuple
        <?xml version="1.0"?>
        <presence
            xmlns="urn:ietf:params:xml:ns:pidf"
            entity="sip:TTbXG7XMO@ws-eu-west-1.innovateasterisk.com">
            <tuple
                xmlns="urn:ietf:params:xml:ns:pidf"
                id="0x7ffce2b4b1a0">
                <status>
                    <basic>open</basic>
                </status>
            </tuple>
            <tuple
                xmlns="urn:ietf:params:xml:ns:pidf"
                id="0x7ffd6abd4a40">
                <status>
                    <basic>open</basic>
                </status>
            </tuple>
        </presence>
"


open: In the context of INSTANT MESSAGES, this value means that the
    associated <contact> element, if any, corresponds to an INSTANT
    INBOX that is ready to accept an INSTANT MESSAGE.

closed: In the context of INSTANT MESSAGES, this value means that
    the associated <contact> element, if any, corresponds to an
    INSTANT INBOX that is unable to accept an INSTANT MESSAGE.

        */

        var xml = $($.parseXML(notification.request.body));

        // The value of the 'entity' attribute is the 'pres' URL of the PRESENT publishing this presence document.
        // (In some cases this can present as the user... what if using DIDs)
        var ObservedUser = xml.find("presence").attr("entity");
        buddy = ObservedUser.split("@")[0].split(":")[1];
        // buddy = xml.find("presence").find("tuple").attr("id"); // Asterisk does this, but its not correct.
        // buddy = notification.request.from.uri.user; // Unreliable

        var availability = "closed"
        // availability = xml.find("presence").find("tuple").find("status").find("basic").text();
        var tuples = xml.find("presence").find("tuple");
        if(tuples){
            $.each(tuples, function(i, obj){
                // So if any of the contacts are open, then say open
                if($(obj).find("status").find("basic").text() == "open") {
                    availability = "open";
                }
            });
        }

        Presence = xml.find("presence").find("note").text();
        if(Presence == ""){
            if (availability == "open") Presence = "Ready";
            if (availability == "closed") Presence = "Not online";
        }
    }
    else if (ContentType == "application/dialog-info+xml") {
        // Handle "Dialog" State

        var xml = $($.parseXML(notification.request.body));

        /*
        Asterisk:
        <?xml version="1.0"?>
        <dialog-info
            xmlns="urn:ietf:params:xml:ns:dialog-info"
            version="0-99999"
            state="full|partial"
            entity="sip:xxxx@XXX.XX.XX.XX">
            <dialog id="xxxx">
                <state>trying | proceeding | early | terminated | confirmed</state>
            </dialog>
        </dialog-info>

        OpenSIPS:
        <?xml version="1.0"?>
        <dialog-info
            xmlns="urn:ietf:params:xml:ns:dialog-info"
            version="18"
            state="full"
            entity="sip:TTbXG7XMO@ws-eu-west-1.innovateasterisk.com"
        />

        <?xml version="1.0"?>
        <dialog-info
            xmlns="urn:ietf:params:xml:ns:dialog-info"
            version="17"
            entity="sip:TTbXG7XMO@ws-eu-west-1.innovateasterisk.com"
            state="partial">
            <dialog
                id="soe2vr886cbn1ccj3h.0"
    *           local-tag="ceq735vrh"
    *           remote-tag="a1d22259-28ea-434f-9680-b925218b7418"
                direction="initiator">
                <state>terminated</state>
    *           <remote>
                    <identity display="Bob">sip:*65@ws-eu-west-1.innovateasterisk.com</identity>
                    <target uri="sip:*65@ws-eu-west-1.innovateasterisk.com"/>
    *           </remote>
    *           <local>
                    <identity display="Conrad De Wet">sip:TTbXG7XMO@ws-eu-west-1.innovateasterisk.com</identity>
                    <target uri="sip:TTbXG7XMO@ws-eu-west-1.innovateasterisk.com"/>
                </local>
            </dialog>
        </dialog-info>
        */

        var ObservedUser = xml.find("dialog-info").attr("entity");
        buddy = ObservedUser.split("@")[0].split(":")[1];

        var version = xml.find("dialog-info").attr("version"); // 1|2|etc
        var DialogState = xml.find("dialog-info").attr("state"); // full|partial
        var extId = xml.find("dialog-info").find("dialog").attr("id"); // qoe2vr886cbn1ccj3h.0

        var state = xml.find("dialog-info").find("dialog").find("state").text();
        if (state == "terminated") Presence = "Ready";
        if (state == "trying") Presence = "On the phone";
        if (state == "proceeding") Presence = "On the phone";
        if (state == "early") Presence = "Ringing";
        if (state == "confirmed") Presence = "On the phone";

        // The dialog states only report devices states, and cant say online or offline.
    }

    if(selfSubscribe){
        if(buddy == SipUsername){
            console.log("Self Notify:", Presence);

            // Custom Handling of Notify/BLF
            if(typeof web_hook_on_self_notify !== 'undefined')  web_hook_on_self_notify(ContentType, notification.request.body);
        }
        else {
            console.warn("Self Subscribe Notify, but wrong user returned.", buddy, SipUsername);
        }
        return;
    }

    var buddyObj = FindBuddyByObservedUser(buddy);
    if(buddyObj == null) {
        console.warn("Buddy not found:", buddy);
        return;
    }

    // dotOnline | dotOffline | dotRinging | dotInUse | dotReady | dotOnHold
    if (Presence == "Not online") dotClass = "dotOffline";
    if (Presence == "Unavailable") dotClass = "dotOffline";
    if (Presence == "Ready") dotClass = "dotOnline";
    if (Presence == "On the phone") dotClass = "dotInUse";
    if (Presence == "Proceeding") dotClass = "dotInUse";
    if (Presence == "Ringing") dotClass = "dotRinging";
    if (Presence == "On hold") dotClass = "dotOnHold";

    // SIP Device Sate indicators
    console.log("Setting DevSate State for "+ buddyObj.CallerIDName +" to "+ dotClass);
    buddyObj.devState = dotClass;
    $("#contact-" + buddyObj.identity + "-devstate").prop("class", dotClass);
    $("#contact-" + buddyObj.identity + "-devstate-main").prop("class", dotClass);

    // Presence (SIP / XMPP)
    // SIP uses Devices states only
    // XMPP uses Device states, and Presence, but only XMPP Presence will display a text message
    if(buddyObj.type != "xmpp"){
        console.log("Setting Presence for "+ buddyObj.CallerIDName +" to "+ Presence);

        buddyObj.presence = Presence;
        if (Presence == "Not online") Presence = lang.state_not_online;
        if (Presence == "Ready") Presence = lang.state_ready;
        if (Presence == "On the phone") Presence = lang.state_on_the_phone;
        if (Presence == "Proceeding") Presence = lang.state_on_the_phone;
        if (Presence == "Ringing") Presence = lang.state_ringing;
        if (Presence == "On hold") Presence = lang.state_on_hold;
        if (Presence == "Unavailable") Presence = lang.state_unavailable;
        $("#contact-" + buddyObj.identity + "-presence").html(Presence);
        $("#contact-" + buddyObj.identity + "-presence-main").html(Presence);
    }

    // Custom Handling of Notify/BLF
    if(typeof web_hook_on_notify !== 'undefined')  web_hook_on_notify(ContentType, buddyObj, notification.request.body);
}

// Buddy: Chat / Instant Message / XMPP
// ====================================
function InitialiseStream(buddy){
    var template = { TotalRows:0, DataCollection:[] }
    localDB.setItem(buddy + "-stream", JSON.stringify(template));
    return JSON.parse(localDB.getItem(buddy + "-stream"));
}
function SendChatMessage(buddy) {
    if (userAgent == null) return;
    if (!userAgent.isRegistered()) return;

    $("#contact-" + buddy + "-ChatMessage").focus(); // refocus on the textarea

    var message = $("#contact-" + buddy + "-ChatMessage").val();
    message = $.trim(message);
    if(message == "") {
        Alert(lang.alert_empty_text_message, lang.no_message);
        return;
    }
    // Note: AMI has this limit, but only if you use AMI to transmit
    // if(message.length > 755){
    //     Alert("Asterisk has a limit on the message size (755). This message is too long, and cannot be delivered.", "Message Too Long");
    //     return;
    // }

    var messageId = uID();
    var buddyObj = FindBuddyByIdentity(buddy);

    // Update Stream
    var DateTime = moment.utc().format("YYYY-MM-DD HH:mm:ss UTC");
    var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
    if(currentStream == null) currentStream = InitialiseStream(buddy);

    // Add New Message
    var newMessageJson = {
        ItemId: messageId,
        ItemType: "MSG",
        ItemDate: DateTime,
        SrcUserId: profileUserID,
        Src: "\""+ profileName +"\"",
        DstUserId: buddyObj.identity,
        Dst: "",
        MessageData: message
    }

    currentStream.DataCollection.push(newMessageJson);
    currentStream.TotalRows = currentStream.DataCollection.length;
    localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));

    // SIP Messages (Note, this may not work as required)
    // ============
    if(buddyObj.type == "extension") {
        var chatBuddy = SIP.UserAgent.makeURI("sip:"+ buddyObj.ExtNo.replace(/#/g, "%23") + "@" + SipDomain);
        console.log("MESSAGE: "+ chatBuddy + " (extension)");


        var MessagerMessageOptions = {
            requestDelegate : {
                onAccept: function(sip){
                    console.log("Message Accepted:", messageId);
                    MarkMessageSent(buddyObj, messageId, true);
                },
                onReject: function(sip){
                    console.warn("Message Error", sip.message.reasonPhrase);
                    MarkMessageNotSent(buddyObj, messageId, true);
                }
            },
            requestOptions : {
                extraHeaders: [],
            }
        }
        var messageObj = new SIP.Messager(userAgent, chatBuddy, message, "text/plain");
        messageObj.message(MessagerMessageOptions).then(function(){
            // Custom Web hook
            if(typeof web_hook_on_message !== 'undefined') web_hook_on_message(messageObj);
        });
    }

    // XMPP Messages
    // =============
    if(buddyObj.type == "xmpp"){
        console.log("MESSAGE: "+ buddyObj.jid + " (xmpp)");
        XmppSendMessage(buddyObj, message, messageId);

        // Custom Web hook
        if(typeof web_hook_on_message !== 'undefined') web_hook_on_message(message);
    }

    // Group Chat
    // ==========
    if(buddyObj.type == "group"){
        // TODO
    }

    // Post Add Activity
    $("#contact-" + buddy + "-ChatMessage").val("");
    $("#contact-" + buddy + "-dictate-message").hide();
    $("#contact-" + buddy + "-emoji-menu").hide();
    $("#contact-" + buddy + "-ChatMessage").focus();

    if(buddyObj.recognition != null){
        buddyObj.recognition.abort();
        buddyObj.recognition = null;
    }

    UpdateBuddyActivity(buddy);
    RefreshStream(buddyObj);
}
function MarkMessageSent(buddyObj, messageId, refresh){
    var currentStream = JSON.parse(localDB.getItem(buddyObj.identity + "-stream"));
    if(currentStream != null || currentStream.DataCollection != null){
        $.each(currentStream.DataCollection, function (i, item) {
            if (item.ItemType == "MSG" && item.ItemId == messageId) {
                // Found
                item.Sent = true;
                return false;
            }
        });
        localDB.setItem(buddyObj.identity + "-stream", JSON.stringify(currentStream));

        if(refresh) RefreshStream(buddyObj);
    }
}
function MarkMessageNotSent(buddyObj, messageId, refresh){
    var currentStream = JSON.parse(localDB.getItem(buddyObj.identity + "-stream"));
    if(currentStream != null || currentStream.DataCollection != null){
        $.each(currentStream.DataCollection, function (i, item) {
            if (item.ItemType == "MSG" && item.ItemId == messageId) {
                // Found
                item.Sent = false;
                return false;
            }
        });
        localDB.setItem(buddyObj.identity + "-stream", JSON.stringify(currentStream));

        if(refresh) RefreshStream(buddyObj);
    }
}
function MarkDeliveryReceipt(buddyObj, messageId, refresh){
    var currentStream = JSON.parse(localDB.getItem(buddyObj.identity + "-stream"));
    if(currentStream != null || currentStream.DataCollection != null){
        $.each(currentStream.DataCollection, function (i, item) {
            if (item.ItemType == "MSG" && item.ItemId == messageId) {
                // Found
                item.Delivered = { state : true, eventTime: utcDateNow()};
                return false;
            }
        });
        localDB.setItem(buddyObj.identity + "-stream", JSON.stringify(currentStream));

        if(refresh) RefreshStream(buddyObj);
    }
}
function MarkDisplayReceipt(buddyObj, messageId, refresh){
    var currentStream = JSON.parse(localDB.getItem(buddyObj.identity + "-stream"));
    if(currentStream != null || currentStream.DataCollection != null){
        $.each(currentStream.DataCollection, function (i, item) {
            if (item.ItemType == "MSG" && item.ItemId == messageId) {
                // Found
                item.Displayed = { state : true, eventTime: utcDateNow()};
                return false;
            }
        });
        localDB.setItem(buddyObj.identity + "-stream", JSON.stringify(currentStream));

        if(refresh) RefreshStream(buddyObj);
    }
}
function MarkMessageRead(buddyObj, messageId){
    var currentStream = JSON.parse(localDB.getItem(buddyObj.identity + "-stream"));
    if(currentStream != null || currentStream.DataCollection != null){
        $.each(currentStream.DataCollection, function (i, item) {
            if (item.ItemType == "MSG" && item.ItemId == messageId) {
                // Found
                item.Read = { state : true, eventTime: utcDateNow()};
                // return false; /// Mark all messages matching that id to avoid
                // duplicate id issue
            }
        });
        localDB.setItem(buddyObj.identity + "-stream", JSON.stringify(currentStream));
        console.log("Set message ("+ messageId +") as Read");
    }
}

function ReceiveOutOfDialogMessage(message) {
    var callerID = message.request.from.displayName;
    var did = message.request.from.uri.normal.user;

    // Out of dialog Message Receiver
    var messageType = (message.request.headers["Content-Type"].length >=1)? message.request.headers["Content-Type"][0].parsed : "Unknown" ;
    // Text Messages
    if(messageType.indexOf("text/plain") > -1){
        // Plain Text Messages SIP SIMPLE
        console.log("New Incoming Message!", "\""+ callerID +"\" <"+ did +">");

        if(did.length > DidLength) {
            // Contacts cannot receive Test Messages, because they cannot reply
            // This may change with FAX, Email, WhatsApp etc
            console.warn("DID length greater then extensions length")
            return;
        }

        var CurrentCalls = countSessions("0");

        var buddyObj = FindBuddyByDid(did);
        // Make new contact of its not there
        if(buddyObj == null) {
            var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
            if(json == null) json = InitUserBuddies();

            // Add Extension
            var id = uID();
            var dateNow = utcDateNow();
            json.DataCollection.push({
                Type: "extension",
                LastActivity: dateNow,
                ExtensionNumber: did,
                MobileNumber: "",
                ContactNumber1: "",
                ContactNumber2: "",
                uID: id,
                cID: null,
                gID: null,
                jid: null,
                DisplayName: callerID,
                Description: "",
                Email: "",
                MemberCount: 0,
                EnableDuringDnd: false,
                Subscribe: false
            });
            buddyObj = new Buddy("extension", id, callerID, did, "", "", "", dateNow, "", "", jid, false, false);

            // Add memory object
            AddBuddy(buddyObj, true, (CurrentCalls==0), false, tue);

            // Update Size:
            json.TotalRows = json.DataCollection.length;

            // Save To DB
            localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));
        }

        var originalMessage = message.request.body;
        var messageId = uID();
        var DateTime = utcDateNow();

        message.accept();

        AddMessageToStream(buddyObj, messageId, "MSG", originalMessage, DateTime)
        UpdateBuddyActivity(buddyObj.identity);
        RefreshStream(buddyObj);
        ActivateStream(buddyObj, originalMessage);
    }
    // Message Summary
    else if(messageType.indexOf("application/simple-message-summary") > -1){
        console.warn("This message-summary is unsolicited (out-of-dialog). Consider using the SUBSCRIBE method.")
        VoicemailNotify(message);
    }
    else{
        console.warn("Unknown Out Of Dialog Message Type: ", messageType);
        message.reject();
    }
    // Custom Web hook
    if(typeof web_hook_on_message !== 'undefined') web_hook_on_message(message);
}
function AddMessageToStream(buddyObj, messageId, type, message, DateTime){
    var currentStream = JSON.parse(localDB.getItem(buddyObj.identity + "-stream"));
    if(currentStream == null) currentStream = InitialiseStream(buddyObj.identity);

    // Add New Message
    var newMessageJson = {
        ItemId: messageId,
        ItemType: type,
        ItemDate: DateTime,
        SrcUserId: buddyObj.identity,
        Src: "\""+ buddyObj.CallerIDName +"\"",
        DstUserId: profileUserID,
        Dst: "",
        MessageData: message
    }

    currentStream.DataCollection.push(newMessageJson);
    currentStream.TotalRows = currentStream.DataCollection.length;
    localDB.setItem(buddyObj.identity + "-stream", JSON.stringify(currentStream));

    // Data Cleanup
    if(MaxDataStoreDays && MaxDataStoreDays > 0){
        console.log("Cleaning up data: ", MaxDataStoreDays);
        RemoveBuddyMessageStream(FindBuddyByIdentity(buddy), MaxDataStoreDays);
    }
}
function ActivateStream(buddyObj, message){
    // Handle Stream Not visible
    // =========================
    var streamVisible = $("#stream-"+ buddyObj.identity).is(":visible");
    if (!streamVisible) {
        // Add or Increase the Badge
        IncreaseMissedBadge(buddyObj.identity);
        if ("Notification" in window) {
            if (Notification.permission === "granted") {
                var imageUrl = getPicture(buddyObj.identity);
                var noticeOptions = { body: message.substring(0, 250), icon: imageUrl }
                var inComingChatNotification = new Notification(lang.message_from + " : " + buddyObj.CallerIDName, noticeOptions);
                inComingChatNotification.onclick = function (event) {
                    // Show Message
                    SelectBuddy(buddyObj.identity);
                }
            }
        }
        // Play Alert
        console.log("Audio:", audioBlobs.Alert.url);
        var ringer = new Audio(audioBlobs.Alert.blob);
        ringer.preload = "auto";
        ringer.loop = false;
        ringer.oncanplaythrough = function(e) {
            if (typeof ringer.sinkId !== 'undefined' && getRingerOutputID() != "default") {
                ringer.setSinkId(getRingerOutputID()).then(function() {
                    console.log("Set sinkId to:", getRingerOutputID());
                }).catch(function(e){
                    console.warn("Failed not apply setSinkId.", e);
                });
            }
            // If there has been no interaction with the page at all... this page will not work
            ringer.play().then(function(){
                // Audio Is Playing
            }).catch(function(e){
                console.warn("Unable to play audio file.", e);
            });
        }
        // message.data.ringerObj = ringer;
    } else {
        // Message window is active.
    }
}
function AddCallMessage(buddy, session) {

    var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
    if(currentStream == null) currentStream = InitialiseStream(buddy);

    var CallEnd = moment.utc(); // Take Now as the Hangup Time
    var callDuration = 0;
    var totalDuration = 0;
    var ringTime = 0;

    var CallStart = moment.utc(session.data.callstart.replace(" UTC", "")); // Actual start (both inbound and outbound)
    var CallAnswer = null; // On Accept when inbound, Remote Side when Outbound
    if(session.data.startTime){
        // The time when WE answered the call (May be null - no answer)
        // or
        // The time when THEY answered the call (May be null - no answer)
        CallAnswer = moment.utc(session.data.startTime);  // Local Time gets converted to UTC

        callDuration = moment.duration(CallEnd.diff(CallAnswer));
        ringTime = moment.duration(CallAnswer.diff(CallStart));
    }
    else {
        // There was no start time, but on inbound/outbound calls, this would indicate the ring time
        ringTime = moment.duration(CallEnd.diff(CallStart));
    }
    totalDuration = moment.duration(CallEnd.diff(CallStart));

    var srcId = "";
    var srcCallerID = "";
    var dstId = ""
    var dstCallerID = "";
    if(session.data.calldirection == "inbound") {
        srcId = buddy;
        dstId = profileUserID;
        srcCallerID = session.remoteIdentity.displayName;
        dstCallerID = profileName;
    } else if(session.data.calldirection == "outbound") {
        srcId = profileUserID;
        dstId = buddy;
        srcCallerID = profileName;
        dstCallerID = session.data.dst;
    }

    var callDirection = session.data.calldirection;
    var withVideo = session.data.withvideo;
    var sessionId = session.id;
    var hangupBy = session.data.terminateby;

    var newMessageJson = {
        CdrId: uID(),
        ItemType: "CDR",
        ItemDate: CallStart.format("YYYY-MM-DD HH:mm:ss UTC"),
        CallAnswer: (CallAnswer)? CallAnswer.format("YYYY-MM-DD HH:mm:ss UTC") : null,
        CallEnd: CallEnd.format("YYYY-MM-DD HH:mm:ss UTC"),
        SrcUserId: srcId,
        Src: srcCallerID,
        DstUserId: dstId,
        Dst: dstCallerID,
        RingTime: (ringTime != 0)? ringTime.asSeconds() : 0,
        Billsec: (callDuration != 0)? callDuration.asSeconds() : 0,
        TotalDuration: (totalDuration != 0)? totalDuration.asSeconds() : 0,
        ReasonCode: session.data.reasonCode,
        ReasonText: session.data.reasonText,
        WithVideo: withVideo,
        SessionId: sessionId,
        CallDirection: callDirection,
        Terminate: hangupBy,
        // CRM
        MessageData: null,
        Tags: [],
        //Reporting
        Transfers: (session.data.transfer)? session.data.transfer : [],
        Mutes: (session.data.mute)? session.data.mute : [],
        Holds: (session.data.hold)? session.data.hold : [],
        Recordings: (session.data.recordings)? session.data.recordings : [],
        ConfCalls: (session.data.confcalls)? session.data.confcalls : [],
        ConfbridgeEvents: (session.data.ConfbridgeEvents)? session.data.ConfbridgeEvents : [],
        QOS: []
    }

    console.log("New CDR", newMessageJson);

    currentStream.DataCollection.push(newMessageJson);
    currentStream.TotalRows = currentStream.DataCollection.length;
    localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));

    UpdateBuddyActivity(buddy);

    // Data Cleanup
    if(MaxDataStoreDays && MaxDataStoreDays > 0){
        console.log("Cleaning up data: ", MaxDataStoreDays);
        RemoveBuddyMessageStream(FindBuddyByIdentity(buddy), MaxDataStoreDays);
    }

}
// TODO
function SendImageDataMessage(buddy, ImgDataUrl) {
    if (userAgent == null) return;
    if (!userAgent.isRegistered()) return;

    // Ajax Upload
    // ===========

    var DateTime = moment.utc().format("YYYY-MM-DD HH:mm:ss UTC");
    var formattedMessage = '<IMG class=previewImage onClick="PreviewImage(this)" src="'+ ImgDataUrl +'">';
    var messageString = "<table class=ourChatMessage cellspacing=0 cellpadding=0><tr><td style=\"width: 80px\">"
        + "<div class=messageDate>" + DateTime + "</div>"
        + "</td><td>"
        + "<div class=ourChatMessageText>" + formattedMessage + "</div>"
        + "</td></tr></table>";
    $("#contact-" + buddy + "-ChatHistory").append(messageString);
    updateScroll(buddy);

    ImageEditor_Cancel(buddy);

    UpdateBuddyActivity(buddy);
}
// TODO
function SendFileDataMessage(buddy, FileDataUrl, fileName, fileSize) {
    if (userAgent == null) return;
    if (!userAgent.isRegistered()) return;

    var fileID = uID();

    // Ajax Upload
    // ===========
    $.ajax({
        type:'POST',
        url: '/api/',
        data: "<XML>"+ FileDataUrl +"</XML>",
        xhr: function(e) {
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){
                myXhr.upload.addEventListener('progress',function(event){
                    var percent = (event.loaded / event.total) * 100;
                    console.log("Progress for upload to "+ buddy +" ("+ fileID +"):"+ percent);
                    $("#FileProgress-Bar-"+ fileID).css("width", percent +"%");
                }, false);
            }
            return myXhr;
        },
        success:function(data, status, jqXHR){
            // console.log(data);
            $("#FileUpload-"+ fileID).html("Sent");
            $("#FileProgress-"+ fileID).hide();
            $("#FileProgress-Bar-"+ fileID).css("width", "0%");
        },
        error: function(data, status, error){
            // console.log(data);
            $("#FileUpload-"+ fileID).html("Failed ("+ data.status +")");
            $("#FileProgress-"+ fileID).hide();
            $("#FileProgress-Bar-"+ fileID).css("width", "100%");
        }
    });

    // Add To Message Stream
    // =====================
    var DateTime = utcDateNow();

    var showReview = false;
    var fileIcon = '<i class="fa fa-file"></i>';
    // Image Icons
    if(fileName.toLowerCase().endsWith(".png")) {
        fileIcon =  '<i class="fa fa-file-image-o"></i>';
        showReview = true;
    }
    if(fileName.toLowerCase().endsWith(".jpg")) {
        fileIcon =  '<i class="fa fa-file-image-o"></i>';
        showReview = true;
    }
    if(fileName.toLowerCase().endsWith(".jpeg")) {
        fileIcon =  '<i class="fa fa-file-image-o"></i>';
        showReview = true;
    }
    if(fileName.toLowerCase().endsWith(".bmp")) {
        fileIcon =  '<i class="fa fa-file-image-o"></i>';
        showReview = true;
    }
    if(fileName.toLowerCase().endsWith(".gif")) {
        fileIcon =  '<i class="fa fa-file-image-o"></i>';
        showReview = true;
    }
    // video Icons
    if(fileName.toLowerCase().endsWith(".mov")) fileIcon =  '<i class="fa fa-file-video-o"></i>';
    if(fileName.toLowerCase().endsWith(".avi")) fileIcon =  '<i class="fa fa-file-video-o"></i>';
    if(fileName.toLowerCase().endsWith(".mpeg")) fileIcon =  '<i class="fa fa-file-video-o"></i>';
    if(fileName.toLowerCase().endsWith(".mp4")) fileIcon =  '<i class="fa fa-file-video-o"></i>';
    if(fileName.toLowerCase().endsWith(".mvk")) fileIcon =  '<i class="fa fa-file-video-o"></i>';
    if(fileName.toLowerCase().endsWith(".webm")) fileIcon =  '<i class="fa fa-file-video-o"></i>';
    // Audio Icons
    if(fileName.toLowerCase().endsWith(".wav")) fileIcon =  '<i class="fa fa-file-audio-o"></i>';
    if(fileName.toLowerCase().endsWith(".mp3")) fileIcon =  '<i class="fa fa-file-audio-o"></i>';
    if(fileName.toLowerCase().endsWith(".ogg")) fileIcon =  '<i class="fa fa-file-audio-o"></i>';
    // Compressed Icons
    if(fileName.toLowerCase().endsWith(".zip")) fileIcon =  '<i class="fa fa-file-archive-o"></i>';
    if(fileName.toLowerCase().endsWith(".rar")) fileIcon =  '<i class="fa fa-file-archive-o"></i>';
    if(fileName.toLowerCase().endsWith(".tar.gz")) fileIcon =  '<i class="fa fa-file-archive-o"></i>';
    // Pdf Icons
    if(fileName.toLowerCase().endsWith(".pdf")) fileIcon =  '<i class="fa fa-file-pdf-o"></i>';

    var formattedMessage = "<DIV><SPAN id=\"FileUpload-"+ fileID +"\">Sending</SPAN>: "+ fileIcon +" "+ fileName +"</DIV>"
    formattedMessage += "<DIV id=\"FileProgress-"+ fileID +"\" class=\"progressBarContainer\"><DIV id=\"FileProgress-Bar-"+ fileID +"\" class=\"progressBarTrack\"></DIV></DIV>"
    if(showReview){
        formattedMessage += "<DIV><IMG class=previewImage onClick=\"PreviewImage(this)\" src=\""+ FileDataUrl +"\"></DIV>";
    }

    var messageString = "<table class=ourChatMessage cellspacing=0 cellpadding=0><tr><td style=\"width: 80px\">"
        + "<div class=messageDate>" + DateTime + "</div>"
        + "</td><td>"
        + "<div class=ourChatMessageText>" + formattedMessage + "</div>"
        + "</td></tr></table>";
    $("#contact-" + buddy + "-ChatHistory").append(messageString);
    updateScroll(buddy);

    ImageEditor_Cancel(buddy);

    // Update Last Activity
    // ====================
    UpdateBuddyActivity(buddy);
}
function updateLineScroll(lineNum) {
    RefreshLineActivity(lineNum);

    var element = $("#line-"+ lineNum +"-CallDetails").get(0);
    if(element) element.scrollTop = element.scrollHeight;
}
function updateScroll(buddy) {
    var history = $("#contact-"+ buddy +"-ChatHistory");
    try{
        if(history.children().length > 0) history.children().last().get(0).scrollIntoView(false);
        history.get(0).scrollTop = history.get(0).scrollHeight;
    } catch(e){}
}
function PreviewImage(obj){
    OpenWindow(obj.src, "Preview Image", 600, 800, false, true); //no close, no resize
}

// Missed Item Notification
// ========================
function IncreaseMissedBadge(buddy) {
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null) return;

    // Up the Missed Count
    // ===================
    buddyObj.missed += 1;

    // Take Out
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    if(json != null) {
        $.each(json.DataCollection, function (i, item) {
            if(item.uID == buddy || item.cID == buddy || item.gID == buddy){
                item.missed = item.missed +1;
                return false;
            }
        });
        // Put Back
        localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));
    }

    // Update Badge
    // ============
    $("#contact-" + buddy + "-missed").text(buddyObj.missed);
    $("#contact-" + buddy + "-missed").show();

    // Custom Web hook
    if(typeof web_hook_on_missed_notify !== 'undefined') web_hook_on_missed_notify(buddyObj.missed);

    console.log("Set Missed badge for "+ buddyObj.CallerIDName +" to: "+ buddyObj.missed);
}
function UpdateBuddyActivity(buddy, lastAct){
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null) return;

    // Update Last Activity Time
    // =========================
    if(lastAct){
        buddyObj.lastActivity = lastAct;
    }
    else {
        var timeStamp = utcDateNow();
        buddyObj.lastActivity = timeStamp;
    }
    console.log("Last Activity for "+  buddyObj.CallerIDName +" is now: "+ buddyObj.lastActivity);

    // Take Out
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    if(json != null) {
        $.each(json.DataCollection, function (i, item) {
            if(item.uID == buddy || item.cID == buddy || item.gID == buddy){
                item.LastActivity = timeStamp;
                return false;
            }
        });
        // Put Back
        localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));
    }

    // List Update
    // ===========
    UpdateBuddyList();
}
function ClearMissedBadge(buddy) {
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null) return;

    buddyObj.missed = 0;

    // Take Out
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    if(json != null) {
        $.each(json.DataCollection, function (i, item) {
            if(item.uID == buddy || item.cID == buddy || item.gID == buddy){
                item.missed = 0;
                return false;
            }
        });
        // Put Back
        localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));
    }

    $("#contact-" + buddy + "-missed").text(buddyObj.missed);
    $("#contact-" + buddy + "-missed").hide(400);

    if(typeof web_hook_on_missed_notify !== 'undefined') web_hook_on_missed_notify(buddyObj.missed);
}

// Outbound Calling
// ================
function VideoCall(lineObj, dialledNumber, extraHeaders) {
    if(userAgent == null) return;
    if(!userAgent.isRegistered()) return;
    if(lineObj == null) return;

    if(HasAudioDevice == false){
        Alert(lang.alert_no_microphone);
        return;
    }

    if(HasVideoDevice == false){
        console.warn("No video devices (webcam) found, switching to audio call.");
        AudioCall(lineObj, dialledNumber);
        return;
    }

    var supportedConstraints = navigator.mediaDevices.getSupportedConstraints();
    var spdOptions = {
        earlyMedia: true,
        sessionDescriptionHandlerOptions: {
            constraints: {
                audio: { deviceId : "default" },
                video: { deviceId : "default" }
            }
        }
    }

    // Configure Audio
    var currentAudioDevice = getAudioSrcID();
    if(currentAudioDevice != "default"){
        var confirmedAudioDevice = false;
        for (var i = 0; i < AudioinputDevices.length; ++i) {
            if(currentAudioDevice == AudioinputDevices[i].deviceId) {
                confirmedAudioDevice = true;
                break;
            }
        }
        if(confirmedAudioDevice) {
            spdOptions.sessionDescriptionHandlerOptions.constraints.audio.deviceId = { exact: currentAudioDevice }
        }
        else {
            console.warn("The audio device you used before is no longer available, default settings applied.");
            localDB.setItem("AudioSrcId", "default");
        }
    }
    // Add additional Constraints
    if(supportedConstraints.autoGainControl) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.autoGainControl = AutoGainControl;
    }
    if(supportedConstraints.echoCancellation) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.echoCancellation = EchoCancellation;
    }
    if(supportedConstraints.noiseSuppression) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.noiseSuppression = NoiseSuppression;
    }

    // Configure Video
    var currentVideoDevice = getVideoSrcID();
    if(currentVideoDevice != "default"){
        var confirmedVideoDevice = false;
        for (var i = 0; i < VideoinputDevices.length; ++i) {
            if(currentVideoDevice == VideoinputDevices[i].deviceId) {
                confirmedVideoDevice = true;
                break;
            }
        }
        if(confirmedVideoDevice){
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.deviceId = { exact: currentVideoDevice }
        }
        else {
            console.warn("The video device you used before is no longer available, default settings applied.");
            localDB.setItem("VideoSrcId", "default"); // resets for later and subsequent calls
        }
    }
    // Add additional Constraints
    if(supportedConstraints.frameRate && maxFrameRate != "") {
        spdOptions.sessionDescriptionHandlerOptions.constraints.video.frameRate = maxFrameRate;
    }
    if(supportedConstraints.height && videoHeight != "") {
        spdOptions.sessionDescriptionHandlerOptions.constraints.video.height = videoHeight;
    }
    if(supportedConstraints.aspectRatio && videoAspectRatio != "") {
        spdOptions.sessionDescriptionHandlerOptions.constraints.video.aspectRatio = videoAspectRatio;
    }
    // Extra Headers
    if(extraHeaders) {
        spdOptions.extraHeaders = extraHeaders;
    }

    $("#line-" + lineObj.LineNumber + "-msg").html(lang.starting_video_call);
    $("#line-" + lineObj.LineNumber + "-timer").show();

    var startTime = moment.utc();

    // Invite
    console.log("INVITE (video): " + dialledNumber + "@" + SipDomain);

    var targetURI = SIP.UserAgent.makeURI("sip:" + dialledNumber.replace(/#/g, "%23") + "@" + SipDomain);
    lineObj.SipSession = new SIP.Inviter(userAgent, targetURI, spdOptions);
    lineObj.SipSession.data = {}
    lineObj.SipSession.data.line = lineObj.LineNumber;
    lineObj.SipSession.data.buddyId = lineObj.BuddyObj.identity;
    lineObj.SipSession.data.calldirection = "outbound";
    lineObj.SipSession.data.dst = dialledNumber;
    lineObj.SipSession.data.callstart = startTime.format("YYYY-MM-DD HH:mm:ss UTC");
    lineObj.SipSession.data.callTimer = window.setInterval(function(){
        var now = moment.utc();
        var duration = moment.duration(now.diff(startTime));
        var timeStr = formatShortDuration(duration.asSeconds());
        $("#line-" + lineObj.LineNumber + "-timer").html(timeStr);
        $("#line-" + lineObj.LineNumber + "-datetime").html(timeStr);
    }, 1000);
    lineObj.SipSession.data.VideoSourceDevice = getVideoSrcID();
    lineObj.SipSession.data.AudioSourceDevice = getAudioSrcID();
    lineObj.SipSession.data.AudioOutputDevice = getAudioOutputID();
    lineObj.SipSession.data.terminateby = "them";
    lineObj.SipSession.data.withvideo = true;
    lineObj.SipSession.data.earlyReject = false;
    lineObj.SipSession.isOnHold = false;
    lineObj.SipSession.delegate = {
        onBye: function(sip){
            onSessionReceivedBye(lineObj, sip);
        },
        onMessage: function(sip){
            onSessionReceivedMessage(lineObj, sip);
        },
        onInvite: function(sip){
            onSessionReinvited(lineObj, sip);
        },
        onSessionDescriptionHandler: function(sdh, provisional){
            onSessionDescriptionHandlerCreated(lineObj, sdh, provisional, true);
        }
    }
    var inviterOptions = {
        requestDelegate: { // OutgoingRequestDelegate
            onTrying: function(sip){
                onInviteTrying(lineObj, sip);
            },
            onProgress:function(sip){
                onInviteProgress(lineObj, sip);
            },
            onRedirect:function(sip){
                onInviteRedirected(lineObj, sip);
            },
            onAccept:function(sip){
                onInviteAccepted(lineObj, true, sip);
            },
            onReject:function(sip){
                onInviteRejected(lineObj, sip);
            }
        }
    }
    lineObj.SipSession.invite(inviterOptions).catch(function(e){
        console.warn("Failed to send INVITE:", e);
    });

    $("#line-" + lineObj.LineNumber + "-btn-settings").removeAttr('disabled');
    $("#line-" + lineObj.LineNumber + "-btn-audioCall").prop('disabled','disabled');
    $("#line-" + lineObj.LineNumber + "-btn-videoCall").prop('disabled','disabled');
    $("#line-" + lineObj.LineNumber + "-btn-search").removeAttr('disabled');

    $("#line-" + lineObj.LineNumber + "-progress").show();
    $("#line-" + lineObj.LineNumber + "-msg").show();

    UpdateUI();
    UpdateBuddyList();
    updateLineScroll(lineObj.LineNumber);

    // Custom Web hook
    if(typeof web_hook_on_invite !== 'undefined') web_hook_on_invite(lineObj.SipSession);
}
function AudioCallMenu(buddy, obj){
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null) return;

    var items = [];
    if(buddyObj.type == "extension" || buddyObj.type == "xmpp") {
        items.push({icon: "fa fa-phone-square", text: lang.call_extension + " ("+ buddyObj.ExtNo +")", value: buddyObj.ExtNo});
        if(buddyObj.MobileNumber != null && buddyObj.MobileNumber != "") {
            items.push({icon: "fa fa-mobile", text: lang.call_mobile + " ("+ buddyObj.MobileNumber +")", value: buddyObj.MobileNumber});
        }
        if(buddyObj.ContactNumber1 != null && buddyObj.ContactNumber1 != "") {
            items.push({icon: "fa fa-phone", text: lang.call_number + " ("+ buddyObj.ContactNumber1 +")", value: buddyObj.ContactNumber1});
        }
        if(buddyObj.ContactNumber2 != null && buddyObj.ContactNumber2 != "") {
            items.push({icon: "fa fa-phone", text: lang.call_number + " ("+ buddyObj.ContactNumber2 +")", value: buddyObj.ContactNumber2});
        }
    }
    else if(buddyObj.type == "contact") {
        if(buddyObj.MobileNumber != null && buddyObj.MobileNumber != "") {
            items.push({icon: "fa fa-mobile", text: lang.call_mobile + " ("+ buddyObj.MobileNumber +")", value: buddyObj.MobileNumber});
        }
        if(buddyObj.ContactNumber1 != null && buddyObj.ContactNumber1 != "") {
            items.push({icon: "fa fa-phone", text: lang.call_number + " ("+ buddyObj.ContactNumber1 +")", value: buddyObj.ContactNumber1});
        }
        if(buddyObj.ContactNumber2 != null && buddyObj.ContactNumber2 != "") {
            items.push({icon: "fa fa-phone", text: lang.call_number + " ("+ buddyObj.ContactNumber2 +")", value: buddyObj.ContactNumber2});
        }
    }
    else if(buddyObj.type == "group") {
        if(buddyObj.MobileNumber != null && buddyObj.MobileNumber != "") {
            items.push({icon: "fa fa-users", text: lang.call_group, value: buddyObj.ExtNo });
        }
    }
    if(items.length == 0) {
        console.error("No numbers to dial");
        EditBuddyWindow(buddy);
        return;
    }
    if(items.length == 1) {
        // only one number provided, call it
        console.log("Automatically calling only number - AudioCall("+ buddy +", "+ items[0].value +")");

        DialByLine("audio", buddy, items[0].value);
    }
    else {
        // Show numbers to dial

        var menu = {
            selectEvent : function( event, ui ) {
                var number = ui.item.attr("value");
                HidePopup();
                if(number != null) {
                    console.log("Menu click AudioCall("+ buddy +", "+ number +")");
                    DialByLine("audio", buddy, number);
                }
            },
            createEvent : null,
            autoFocus : true,
            items : items
        }
        PopupMenu(obj, menu);
    }
}
function AudioCall(lineObj, dialledNumber, extraHeaders) {
    if(userAgent == null) return;
    if(userAgent.isRegistered() == false) return;
    if(lineObj == null) return;

    if(HasAudioDevice == false){
        Alert(lang.alert_no_microphone);
        return;
    }

    var supportedConstraints = navigator.mediaDevices.getSupportedConstraints();

    var spdOptions = {
        earlyMedia: true,
        sessionDescriptionHandlerOptions: {
            constraints: {
                audio: { deviceId : "default" },
                video: false
            }
        }
    }
    // Configure Audio
    var currentAudioDevice = getAudioSrcID();
    if(currentAudioDevice != "default"){
        var confirmedAudioDevice = false;
        for (var i = 0; i < AudioinputDevices.length; ++i) {
            if(currentAudioDevice == AudioinputDevices[i].deviceId) {
                confirmedAudioDevice = true;
                break;
            }
        }
        if(confirmedAudioDevice) {
            spdOptions.sessionDescriptionHandlerOptions.constraints.audio.deviceId = { exact: currentAudioDevice }
        }
        else {
            console.warn("The audio device you used before is no longer available, default settings applied.");
            localDB.setItem("AudioSrcId", "default");
        }
    }
    // Add additional Constraints
    if(supportedConstraints.autoGainControl) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.autoGainControl = AutoGainControl;
    }
    if(supportedConstraints.echoCancellation) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.echoCancellation = EchoCancellation;
    }
    if(supportedConstraints.noiseSuppression) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.noiseSuppression = NoiseSuppression;
    }
    // Extra Headers
    if(extraHeaders) {
        spdOptions.extraHeaders = extraHeaders;
    }

    $("#line-" + lineObj.LineNumber + "-msg").html(lang.starting_audio_call);
    $("#line-" + lineObj.LineNumber + "-timer").show();

    var startTime = moment.utc();

    // Invite
    console.log("INVITE (audio): " + dialledNumber + "@" + SipDomain);

    var targetURI = SIP.UserAgent.makeURI("sip:" + dialledNumber.replace(/#/g, "%23") + "@" + SipDomain);
    lineObj.SipSession = new SIP.Inviter(userAgent, targetURI, spdOptions);
    lineObj.SipSession.data = {}
    lineObj.SipSession.data.line = lineObj.LineNumber;
    lineObj.SipSession.data.buddyId = lineObj.BuddyObj.identity;
    lineObj.SipSession.data.calldirection = "outbound";
    lineObj.SipSession.data.dst = dialledNumber;
    lineObj.SipSession.data.callstart = startTime.format("YYYY-MM-DD HH:mm:ss UTC");
    lineObj.SipSession.data.callTimer = window.setInterval(function(){
        var now = moment.utc();
        var duration = moment.duration(now.diff(startTime));
        var timeStr = formatShortDuration(duration.asSeconds());
        $("#line-" + lineObj.LineNumber + "-timer").html(timeStr);
        $("#line-" + lineObj.LineNumber + "-datetime").html(timeStr);
    }, 1000);
    lineObj.SipSession.data.VideoSourceDevice = null;
    lineObj.SipSession.data.AudioSourceDevice = getAudioSrcID();
    lineObj.SipSession.data.AudioOutputDevice = getAudioOutputID();
    lineObj.SipSession.data.terminateby = "them";
    lineObj.SipSession.data.withvideo = false;
    lineObj.SipSession.data.earlyReject = false;
    lineObj.SipSession.isOnHold = false;
    lineObj.SipSession.delegate = {
        onBye: function(sip){
            onSessionReceivedBye(lineObj, sip);
        },
        onMessage: function(sip){
            onSessionReceivedMessage(lineObj, sip);
        },
        onInvite: function(sip){
            onSessionReinvited(lineObj, sip);
        },
        onSessionDescriptionHandler: function(sdh, provisional){
            onSessionDescriptionHandlerCreated(lineObj, sdh, provisional, false);
        }
    }
    var inviterOptions = {
        requestDelegate: { // OutgoingRequestDelegate
            onTrying: function(sip){
                onInviteTrying(lineObj, sip);
            },
            onProgress:function(sip){
                onInviteProgress(lineObj, sip);
            },
            onRedirect:function(sip){
                onInviteRedirected(lineObj, sip);
            },
            onAccept:function(sip){
                onInviteAccepted(lineObj, false, sip);
            },
            onReject:function(sip){
                onInviteRejected(lineObj, sip);
            }
        }
    }
    lineObj.SipSession.invite(inviterOptions).catch(function(e){
        console.warn("Failed to send INVITE:", e);
    });

    $("#line-" + lineObj.LineNumber + "-btn-settings").removeAttr('disabled');
    $("#line-" + lineObj.LineNumber + "-btn-audioCall").prop('disabled','disabled');
    $("#line-" + lineObj.LineNumber + "-btn-videoCall").prop('disabled','disabled');
    $("#line-" + lineObj.LineNumber + "-btn-search").removeAttr('disabled');

    $("#line-" + lineObj.LineNumber + "-progress").show();
    $("#line-" + lineObj.LineNumber + "-msg").show();

    UpdateUI();
    UpdateBuddyList();
    updateLineScroll(lineObj.LineNumber);

    // Custom Web hook
    if(typeof web_hook_on_invite !== 'undefined') web_hook_on_invite(lineObj.SipSession);
}

// Sessions & During Call Activity
// ===============================
function getSession(buddy) {
    if(userAgent == null) {
        console.warn("userAgent is null");
        return null;
    }
    if(userAgent.isRegistered() == false) {
        console.warn("userAgent is not registered");
        return null;
    }

    var rtnSession = null;
    $.each(userAgent.sessions, function (i, session) {
        if(session.data.buddyId == buddy) {
            rtnSession = session;
            return false;
        }
    });
    return rtnSession;
}
function countSessions(id){
    var rtn = 0;
    if(userAgent == null) {
        console.warn("userAgent is null");
        return 0;
    }
    $.each(userAgent.sessions, function (i, session) {
        if(id != session.id) rtn ++;
    });
    return rtn;
}
function StartRecording(lineNum){
    if(CallRecordingPolicy == "disabled") {
        console.warn("Policy Disabled: Call Recording");
        return;
    }
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null) return;

    $("#line-"+ lineObj.LineNumber +"-btn-start-recording").hide();
    $("#line-"+ lineObj.LineNumber +"-btn-stop-recording").show();

    var session = lineObj.SipSession;
    if(session == null){
        console.warn("Could not find session");
        return;
    }

    var id = uID();

    if(!session.data.recordings) session.data.recordings = [];
    session.data.recordings.push({
        uID: id,
        startTime: utcDateNow(),
        stopTime: utcDateNow(),
    });

    if(session.data.mediaRecorder && session.data.mediaRecorder.state == "recording"){
        console.warn("Call Recording was somehow on... stopping call recording");
        StopRecording(lineNum, true);
        // State should be inactive now, but the data available event will fire
        // Note: potential race condition here if someone hits the stop, and start quite quickly.
    }
    console.log("Creating call recorder...");

    session.data.recordingAudioStreams = new MediaStream();
    var pc = session.sessionDescriptionHandler.peerConnection;
    pc.getSenders().forEach(function (RTCRtpSender) {
        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
            console.log("Adding sender audio track to record:", RTCRtpSender.track.label);
            session.data.recordingAudioStreams.addTrack(RTCRtpSender.track);
        }
    });
    pc.getReceivers().forEach(function (RTCRtpReceiver) {
        if(RTCRtpReceiver.track && RTCRtpReceiver.track.kind == "audio") {
            console.log("Adding receiver audio track to record:", RTCRtpReceiver.track.label);
            session.data.recordingAudioStreams.addTrack(RTCRtpReceiver.track);
        }
    });

    // Resample the Video Recording
    if(session.data.withvideo){
        var recordingWidth = 640;
        var recordingHeight = 360;
        var pnpVideSize = 100;
        if(RecordingVideoSize == "HD"){
            recordingWidth = 1280;
            recordingHeight = 720;
            pnpVideSize = 144;
        }
        if(RecordingVideoSize == "FHD"){
            recordingWidth = 1920;
            recordingHeight = 1080;
            pnpVideSize = 240;
        }
        // Create Canvas
        session.data.recordingCanvas = $('<canvas/>').get(0);
        session.data.recordingCanvas.width = (RecordingLayout == "side-by-side")? (recordingWidth * 2) + 5: recordingWidth;
        session.data.recordingCanvas.height = recordingHeight;
        session.data.recordingContext = session.data.recordingCanvas.getContext("2d");

        // Capture Interval
        window.clearInterval(session.data.recordingRedrawInterval);
        session.data.recordingRedrawInterval = window.setInterval(function(){

            // Video Source
            var pnpVideo = $("#line-" + lineObj.LineNumber + "-localVideo").get(0);

            var mainVideo = null;
            var validVideos = [];
            var talkingVideos = [];
            var videoContainer = $("#line-" + lineObj.LineNumber + "-remote-videos");
            var potentialVideos =  videoContainer.find('video').length;
            if(potentialVideos == 0){
                // Nothing to render
                // console.log("Nothing to render in this frame")
            }
            else if (potentialVideos == 1){
                mainVideo = videoContainer.find('video')[0];
                // console.log("Only one video element", mainVideo);
            }
            else if (potentialVideos > 1){
                // Decide what video to record
                videoContainer.find('video').each(function(i, video) {
                    var videoTrack = video.srcObject.getVideoTracks()[0];
                    if(videoTrack.readyState == "live" && video.videoWidth > 10 && video.videoHeight >= 10) {
                        if(video.srcObject.isPinned == true){
                            mainVideo = video;
                            // console.log("Multiple Videos using last PINNED frame");
                        }
                        if(video.srcObject.isTalking == true){
                            talkingVideos.push(video);
                        }
                        validVideos.push(video);
                    }
                });

                // Check if we found something
                if(mainVideo == null && talkingVideos.length >= 1){
                    // Nothing pinned use talking
                    mainVideo = talkingVideos[0];
                    // console.log("Multiple Videos using first talking frame");
                }
                if(mainVideo == null && validVideos.length >= 1){
                    // Nothing pinned or talking use valid
                    mainVideo = validVideos[0];
                    // console.log("Multiple Videos using first VALID frame");
                }
            }

            // Main Video
            var videoWidth = (mainVideo && mainVideo.videoWidth > 0)? mainVideo.videoWidth : recordingWidth ;
            var videoHeight = (mainVideo && mainVideo.videoHeight > 0)? mainVideo.videoHeight : recordingHeight ;
            if(videoWidth >= videoHeight){
                // Landscape / Square
                var scale = recordingWidth / videoWidth;
                videoWidth = recordingWidth;
                videoHeight = videoHeight * scale;
                if(videoHeight > recordingHeight){
                    var scale = recordingHeight / videoHeight;
                    videoHeight = recordingHeight;
                    videoWidth = videoWidth * scale;
                }
            }
            else {
                // Portrait
                var scale = recordingHeight / videoHeight;
                videoHeight = recordingHeight;
                videoWidth = videoWidth * scale;
            }
            var offsetX = (videoWidth < recordingWidth)? (recordingWidth - videoWidth) / 2 : 0;
            var offsetY = (videoHeight < recordingHeight)? (recordingHeight - videoHeight) / 2 : 0;
            if(RecordingLayout == "side-by-side") offsetX = recordingWidth + 5 + offsetX;

            // Picture-in-Picture Video
            var pnpVideoHeight = pnpVideo.videoHeight;
            var pnpVideoWidth = pnpVideo.videoWidth;
            if(pnpVideoHeight > 0){
                if(pnpVideoWidth >= pnpVideoHeight){
                    var scale = pnpVideSize / pnpVideoHeight;
                    pnpVideoHeight = pnpVideSize;
                    pnpVideoWidth = pnpVideoWidth * scale;
                }
                else{
                    var scale = pnpVideSize / pnpVideoWidth;
                    pnpVideoWidth = pnpVideSize;
                    pnpVideoHeight = pnpVideoHeight * scale;
                }
            }
            var pnpOffsetX = 10;
            var pnpOffsetY = 10;
            if(RecordingLayout == "side-by-side"){
                pnpVideoWidth = pnpVideo.videoWidth;
                pnpVideoHeight = pnpVideo.videoHeight;
                if(pnpVideoWidth >= pnpVideoHeight){
                    // Landscape / Square
                    var scale = recordingWidth / pnpVideoWidth;
                    pnpVideoWidth = recordingWidth;
                    pnpVideoHeight = pnpVideoHeight * scale;
                    if(pnpVideoHeight > recordingHeight){
                        var scale = recordingHeight / pnpVideoHeight;
                        pnpVideoHeight = recordingHeight;
                        pnpVideoWidth = pnpVideoWidth * scale;
                    }
                }
                else {
                    // Portrait
                    var scale = recordingHeight / pnpVideoHeight;
                    pnpVideoHeight = recordingHeight;
                    pnpVideoWidth = pnpVideoWidth * scale;
                }
                pnpOffsetX = (pnpVideoWidth < recordingWidth)? (recordingWidth - pnpVideoWidth) / 2 : 0;
                pnpOffsetY = (pnpVideoHeight < recordingHeight)? (recordingHeight - pnpVideoHeight) / 2 : 0;
            }

            // Draw Background
            session.data.recordingContext.fillRect(0, 0, session.data.recordingCanvas.width, session.data.recordingCanvas.height);

            // Draw Main Video
            if(mainVideo && mainVideo.videoHeight > 0){
                session.data.recordingContext.drawImage(mainVideo, offsetX, offsetY, videoWidth, videoHeight);
            }

            // Draw PnP
            if(pnpVideo.videoHeight > 0 && (RecordingLayout == "side-by-side" || RecordingLayout == "them-pnp")){
                // Only Draw the Pnp Video when needed
                session.data.recordingContext.drawImage(pnpVideo, pnpOffsetX, pnpOffsetY, pnpVideoWidth, pnpVideoHeight);
            }
        }, Math.floor(1000/RecordingVideoFps));

        // Start Video Capture
        session.data.recordingVideoMediaStream = session.data.recordingCanvas.captureStream(RecordingVideoFps);
    }

    session.data.recordingMixedAudioVideoRecordStream = new MediaStream();
    session.data.recordingMixedAudioVideoRecordStream.addTrack(MixAudioStreams(session.data.recordingAudioStreams).getAudioTracks()[0]);
    if(session.data.withvideo){
        session.data.recordingMixedAudioVideoRecordStream.addTrack(session.data.recordingVideoMediaStream.getVideoTracks()[0]);
    }

    var mediaType = "audio/webm"; // audio/mp4 | audio/webm;
    if(session.data.withvideo) mediaType = "video/webm";
    var options = {
        mimeType : mediaType
    }
    // Note: It appears that mimeType is optional, but... Safari is truly dreadful at recording in mp4, and doesn't have webm yet
    // You you can leave this as default, or force webm, however know that Safari will be no good at this either way.
    // session.data.mediaRecorder = new MediaRecorder(session.data.recordingMixedAudioVideoRecordStream, options);
    session.data.mediaRecorder = new MediaRecorder(session.data.recordingMixedAudioVideoRecordStream);
    session.data.mediaRecorder.data = {}
    session.data.mediaRecorder.data.id = ""+ id;
    session.data.mediaRecorder.data.sessionId = ""+ session.id;
    session.data.mediaRecorder.data.buddyId = ""+ lineObj.BuddyObj.identity;
    session.data.mediaRecorder.ondataavailable = function(event) {
        console.log("Got Call Recording Data: ", event.data.size +"Bytes", this.data.id, this.data.buddyId, this.data.sessionId);
        // Save the Audio/Video file
        SaveCallRecording(event.data, this.data.id, this.data.buddyId, this.data.sessionId);
    }

    console.log("Starting Call Recording", id);
    session.data.mediaRecorder.start(); // Safari does not support time slice
    session.data.recordings[session.data.recordings.length-1].startTime = utcDateNow();

    $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_recording_started);

    updateLineScroll(lineNum);
}
function SaveCallRecording(blob, id, buddy, sessionid){
    var indexedDB = window.indexedDB;
    var request = indexedDB.open("CallRecordings", 1);
    request.onerror = function(event) {
        console.error("IndexDB Request Error:", event);
    }
    request.onupgradeneeded = function(event) {
        console.warn("Upgrade Required for IndexDB... probably because of first time use.");
        var IDB = event.target.result;

        // Create Object Store
        if(IDB.objectStoreNames.contains("Recordings") == false){
            var objectStore = IDB.createObjectStore("Recordings", { keyPath: "uID" });
            objectStore.createIndex("sessionid", "sessionid", { unique: false });
            objectStore.createIndex("bytes", "bytes", { unique: false });
            objectStore.createIndex("type", "type", { unique: false });
            objectStore.createIndex("mediaBlob", "mediaBlob", { unique: false });
        }
        else {
            console.warn("IndexDB requested upgrade, but object store was in place.");
        }
    }
    request.onsuccess = function(event) {
        console.log("IndexDB connected to CallRecordings");

        var IDB = event.target.result;
        if(IDB.objectStoreNames.contains("Recordings") == false){
            console.warn("IndexDB CallRecordings.Recordings does not exists, this call recoding will not be saved.");
            IDB.close();
            window.indexedDB.deleteDatabase("CallRecordings"); // This should help if the table structure has not been created.
            return;
        }
        IDB.onerror = function(event) {
            console.error("IndexDB Error:", event);
        }

        // Prepare data to write
        var data = {
            uID: id,
            sessionid: sessionid,
            bytes: blob.size,
            type: blob.type,
            mediaBlob: blob
        }
        // Commit Transaction
        var transaction = IDB.transaction(["Recordings"], "readwrite");
        var objectStoreAdd = transaction.objectStore("Recordings").add(data);
        objectStoreAdd.onsuccess = function(event) {
            console.log("Call Recording Success: ", id, blob.size, blob.type, buddy, sessionid);
        }
    }
}
function StopRecording(lineNum, noConfirm){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;

    var session = lineObj.SipSession;
    if(noConfirm == true){
        // Called at the end of a call
        $("#line-"+ lineObj.LineNumber +"-btn-start-recording").show();
        $("#line-"+ lineObj.LineNumber +"-btn-stop-recording").hide();

        if(session.data.mediaRecorder){
            if(session.data.mediaRecorder.state == "recording"){
                console.log("Stopping Call Recording");
                session.data.mediaRecorder.stop();
                session.data.recordings[session.data.recordings.length-1].stopTime = utcDateNow();
                window.clearInterval(session.data.recordingRedrawInterval);

                $("#line-" + lineObj.LineNumber + "-msg").html(lang.call_recording_stopped);

                updateLineScroll(lineNum);
            }
            else{
                console.warn("Recorder is in an unknown state");
            }
        }
        return;
    }
    else {
        // User attempts to end call recording
        if(CallRecordingPolicy == "enabled"){
            console.warn("Policy Enabled: Call Recording");
            return;
        }

        Confirm(lang.confirm_stop_recording, lang.stop_recording, function(){
            StopRecording(lineNum, true);
        });
    }
}
function PlayAudioCallRecording(obj, cdrId, uID){
    var container = $(obj).parent();
    container.empty();

    var audioObj = new Audio();
    audioObj.autoplay = false;
    audioObj.controls = true;

    // Make sure you are playing out via the correct device
    var sinkId = getAudioOutputID();
    if (typeof audioObj.sinkId !== 'undefined') {
        audioObj.setSinkId(sinkId).then(function(){
            console.log("sinkId applied: "+ sinkId);
        }).catch(function(e){
            console.warn("Error using setSinkId: ", e);
        });
    } else {
        console.warn("setSinkId() is not possible using this browser.")
    }

    container.append(audioObj);

    // Get Call Recording
    var indexedDB = window.indexedDB;
    var request = indexedDB.open("CallRecordings", 1);
    request.onerror = function(event) {
        console.error("IndexDB Request Error:", event);
    }
    request.onupgradeneeded = function(event) {
        console.warn("Upgrade Required for IndexDB... probably because of first time use.");
    }
    request.onsuccess = function(event) {
        console.log("IndexDB connected to CallRecordings");

        var IDB = event.target.result;
        if(IDB.objectStoreNames.contains("Recordings") == false){
            console.warn("IndexDB CallRecordings.Recordings does not exists");
            return;
        }

        var transaction = IDB.transaction(["Recordings"]);
        var objectStoreGet = transaction.objectStore("Recordings").get(uID);
        objectStoreGet.onerror = function(event) {
            console.error("IndexDB Get Error:", event);
        }
        objectStoreGet.onsuccess = function(event) {
            $("#cdr-media-meta-size-"+ cdrId +"-"+ uID).html(" Size: "+ formatBytes(event.target.result.bytes));
            $("#cdr-media-meta-codec-"+ cdrId +"-"+ uID).html(" Codec: "+ event.target.result.type);

            // Play
            audioObj.src = window.URL.createObjectURL(event.target.result.mediaBlob);
            audioObj.oncanplaythrough = function(){
                audioObj.play().then(function(){
                    console.log("Playback started");
                }).catch(function(e){
                    console.error("Error playing back file: ", e);
                });
            }
        }
    }
}
function PlayVideoCallRecording(obj, cdrId, uID, buddy){
    var container = $(obj).parent();
    container.empty();

    var videoObj = $("<video>").get(0);
    videoObj.id = "callrecording-video-"+ cdrId;
    videoObj.autoplay = false;
    videoObj.controls = true;
    videoObj.playsinline = true;
    videoObj.ontimeupdate = function(event){
        $("#cdr-video-meta-width-"+ cdrId +"-"+ uID).html(lang.width + " : "+ event.target.videoWidth +"px");
        $("#cdr-video-meta-height-"+ cdrId +"-"+ uID).html(lang.height +" : "+ event.target.videoHeight +"px");
    }

    var sinkId = getAudioOutputID();
    if (typeof videoObj.sinkId !== 'undefined') {
        videoObj.setSinkId(sinkId).then(function(){
            console.log("sinkId applied: "+ sinkId);
        }).catch(function(e){
            console.warn("Error using setSinkId: ", e);
        });
    } else {
        console.warn("setSinkId() is not possible using this browser.")
    }

    container.append(videoObj);

    // Get Call Recording
    var indexedDB = window.indexedDB;
    var request = indexedDB.open("CallRecordings", 1);
    request.onerror = function(event) {
        console.error("IndexDB Request Error:", event);
    }
    request.onupgradeneeded = function(event) {
        console.warn("Upgrade Required for IndexDB... probably because of first time use.");
    }
    request.onsuccess = function(event) {
        console.log("IndexDB connected to CallRecordings");

        var IDB = event.target.result;
        if(IDB.objectStoreNames.contains("Recordings") == false){
            console.warn("IndexDB CallRecordings.Recordings does not exists");
            return;
        }

        var transaction = IDB.transaction(["Recordings"]);
        var objectStoreGet = transaction.objectStore("Recordings").get(uID);
        objectStoreGet.onerror = function(event) {
            console.error("IndexDB Get Error:", event);
        }
        objectStoreGet.onsuccess = function(event) {
            $("#cdr-media-meta-size-"+ cdrId +"-"+ uID).html(" Size: "+ formatBytes(event.target.result.bytes));
            $("#cdr-media-meta-codec-"+ cdrId +"-"+ uID).html(" Codec: "+ event.target.result.type);

            // Play
            videoObj.src = window.URL.createObjectURL(event.target.result.mediaBlob);
            videoObj.oncanplaythrough = function(){
                try{
                    videoObj.scrollIntoViewIfNeeded(false);
                } catch(e){}
                videoObj.play().then(function(){
                    console.log("Playback started");
                }).catch(function(e){
                    console.error("Error playing back file: ", e);
                });

                // Create a Post Image after a second
                if(buddy){
                    window.setTimeout(function(){
                        var canvas = $("<canvas>").get(0);
                        var videoWidth = videoObj.videoWidth;
                        var videoHeight = videoObj.videoHeight;
                        if(videoWidth > videoHeight){
                            // Landscape
                            if(videoHeight > 225){
                                var p = 225 / videoHeight;
                                videoHeight = 225;
                                videoWidth = videoWidth * p;
                            }
                        }
                        else {
                            // Portrait
                            if(videoHeight > 225){
                                var p = 225 / videoWidth;
                                videoWidth = 225;
                                videoHeight = videoHeight * p;
                            }
                        }
                        canvas.width = videoWidth;
                        canvas.height = videoHeight;
                        canvas.getContext('2d').drawImage(videoObj, 0, 0, videoWidth, videoHeight);
                        canvas.toBlob(function(blob) {
                            var reader = new FileReader();
                            reader.readAsDataURL(blob);
                            reader.onloadend = function() {
                                var Poster = { width: videoWidth, height: videoHeight, posterBase64: reader.result }
                                console.log("Capturing Video Poster...");

                                // Update DB
                                var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
                                if(currentStream != null || currentStream.DataCollection != null){
                                    $.each(currentStream.DataCollection, function(i, item) {
                                        if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                                            // Found
                                            if(item.Recordings && item.Recordings.length >= 1){
                                                $.each(item.Recordings, function(r, recording) {
                                                    if(recording.uID == uID) recording.Poster = Poster;
                                                });
                                            }
                                            return false;
                                        }
                                    });
                                    localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));
                                    console.log("Capturing Video Poster, Done");
                                }
                            }
                        }, 'image/jpeg', PosterJpegQuality);
                    }, 1000);
                }
            }
        }
    }
}

// Stream Manipulations
// ====================
function MixAudioStreams(MultiAudioTackStream){
    // Takes in a MediaStream with any number of audio tracks and mixes them together

    var audioContext = null;
    try {
        window.AudioContext = window.AudioContext || window.webkitAudioContext;
        audioContext = new AudioContext();
    }
    catch(e){
        console.warn("AudioContext() not available, cannot record");
        return MultiAudioTackStream;
    }
    var mixedAudioStream = audioContext.createMediaStreamDestination();
    MultiAudioTackStream.getAudioTracks().forEach(function(audioTrack){
        var srcStream = new MediaStream();
        srcStream.addTrack(audioTrack);
        var streamSourceNode = audioContext.createMediaStreamSource(srcStream);
        streamSourceNode.connect(mixedAudioStream);
    });

    return mixedAudioStream.stream;
}

// Call Transfer & Conference
// ============================
function QuickFindBuddy(obj){
    var filter = obj.value;
    if(filter == "") {
        HidePopup();
        return;
    }

    console.log("Find Buddy: ", filter);

    Buddies.sort(function(a, b){
        if(a.CallerIDName < b.CallerIDName) return -1;
        if(a.CallerIDName > b.CallerIDName) return 1;
        return 0;
    });

    var items = [];
    var visibleItems = 0;
    for(var b = 0; b < Buddies.length; b++){
        var buddyObj = Buddies[b];

        // Perform Filter Display
        var display = false;
        if(buddyObj.CallerIDName && buddyObj.CallerIDName.toLowerCase().indexOf(filter.toLowerCase()) > -1) display = true;
        if(buddyObj.ExtNo && buddyObj.ExtNo.toLowerCase().indexOf(filter.toLowerCase()) > -1) display = true;
        if(buddyObj.Desc && buddyObj.Desc.toLowerCase().indexOf(filter.toLowerCase()) > -1) display = true;
        if(buddyObj.MobileNumber && buddyObj.MobileNumber.toLowerCase().indexOf(filter.toLowerCase()) > -1) display = true;
        if(buddyObj.ContactNumber1 && buddyObj.ContactNumber1.toLowerCase().indexOf(filter.toLowerCase()) > -1) display = true;
        if(buddyObj.ContactNumber2 && buddyObj.ContactNumber2.toLowerCase().indexOf(filter.toLowerCase()) > -1) display = true;
        if(display) {
            // Filtered Results
            var iconClass = "dotDefault";
            if(buddyObj.type == "extension" && buddyObj.EnableSubscribe == true) {
                iconClass = buddyObj.devState;
            } else if(buddyObj.type == "xmpp" && buddyObj.EnableSubscribe == true) {
                iconClass = buddyObj.devState;
            }
            if(visibleItems > 0) items.push({ value: null, text: "-"});
            items.push({ value: null, text: buddyObj.CallerIDName, isHeader: true });
            if(buddyObj.ExtNo != "") {
                items.push({ icon : "fa fa-phone-square "+ iconClass, text: lang.extension +" ("+ buddyObj.presence +"): "+ buddyObj.ExtNo, value: buddyObj.ExtNo });
            }
            if(buddyObj.MobileNumber != "") {
                items.push({ icon : "fa fa-mobile", text: lang.mobile +": "+ buddyObj.MobileNumber, value: buddyObj.MobileNumber });
            }
            if(buddyObj.ContactNumber1 != "") {
                items.push({ icon : "fa fa-phone", text: lang.call +": "+ buddyObj.ContactNumber1, value: buddyObj.ContactNumber1 });
            }
            if(buddyObj.ContactNumber2 != "") {
                items.push({ icon : "fa fa-phone", text: lang.call +": "+ buddyObj.ContactNumber2, value: buddyObj.ContactNumber2 });
            }
            visibleItems++;
        }
        if(visibleItems >= 5) break;
    }

    if(items.length > 1){
        var menu = {
            selectEvent : function( event, ui ) {
                var number = ui.item.attr("value");
                if(number == null) HidePopup();
                if(number != "null" && number != "" && number != undefined) {
                    HidePopup();
                    obj.value = number;
                }
            },
            createEvent : null,
            autoFocus : false,
            items : items
        }
        PopupMenu(obj, menu);
    }
    else {
        HidePopup();
    }
}

// Call Transfer
// =============
function StartTransferSession(lineNum){
    if($("#line-"+ lineNum +"-btn-CancelConference").is(":visible")){
        CancelConference(lineNum);
        return;
    }

    $("#line-"+ lineNum +"-btn-Transfer").hide();
    $("#line-"+ lineNum +"-btn-CancelTransfer").show();

    holdSession(lineNum);
    $("#line-"+ lineNum +"-txt-FindTransferBuddy").val("");
    $("#line-"+ lineNum +"-txt-FindTransferBuddy").parent().show();

    $("#line-"+ lineNum +"-session-avatar").css("width", "50px");
    $("#line-"+ lineNum +"-session-avatar").css("height", "50px");
    RestoreCallControls(lineNum)

    $("#line-"+ lineNum +"-btn-blind-transfer").show();
    $("#line-"+ lineNum +"-btn-attended-transfer").show();
    $("#line-"+ lineNum +"-btn-complete-transfer").hide();
    $("#line-"+ lineNum +"-btn-cancel-transfer").hide();

    $("#line-"+ lineNum +"-btn-complete-attended-transfer").hide();
    $("#line-"+ lineNum +"-btn-cancel-attended-transfer").hide();
    $("#line-"+ lineNum +"-btn-terminate-attended-transfer").hide();

    $("#line-"+ lineNum +"-transfer-status").hide();

    $("#line-"+ lineNum +"-Transfer").show();

    updateLineScroll(lineNum);
}
function CancelTransferSession(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Null line or session");
        return;
    }
    var session = lineObj.SipSession;
    if(session.data.childsession){
        console.log("Child Transfer call detected:", session.data.childsession.state);
        session.data.childsession.dispose().then(function(){
            session.data.childsession = null;
        }).catch(function(error){
            session.data.childsession = null;
            // Suppress message
        });
    }

    $("#line-"+ lineNum +"-session-avatar").css("width", "");
    $("#line-"+ lineNum +"-session-avatar").css("height", "");

    $("#line-"+ lineNum +"-btn-Transfer").show();
    $("#line-"+ lineNum +"-btn-CancelTransfer").hide();

    unholdSession(lineNum);
    $("#line-"+ lineNum +"-Transfer").hide();

    updateLineScroll(lineNum);
}
function transferOnkeydown(event, obj, lineNum) {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13'){
        event.preventDefault();
        if(event.ctrlKey){
            AttendedTransfer(lineNum);
        }
        else {
            BlindTransfer(lineNum);
        }

        return false;
    }
}
function BlindTransfer(lineNum) {
    var dstNo = $("#line-"+ lineNum +"-txt-FindTransferBuddy").val();
    if(EnableAlphanumericDial){
        dstNo = dstNo.replace(telAlphanumericRegEx, "").substring(0,MaxDidLength);
    }
    else {
        dstNo = dstNo.replace(telNumericRegEx, "").substring(0,MaxDidLength);
    }
    if(dstNo == ""){
        console.warn("Cannot transfer, no number");
        return;
    }

    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Null line or session");
        return;
    }
    var session = lineObj.SipSession;

    if(!session.data.transfer) session.data.transfer = [];
    session.data.transfer.push({
        type: "Blind",
        to: dstNo,
        transferTime: utcDateNow(),
        disposition: "refer",
        dispositionTime: utcDateNow(),
        accept : {
            complete: null,
            eventTime: null,
            disposition: ""
        }
    });
    var transferId = session.data.transfer.length-1;

    var transferOptions  = {
        requestDelegate: {
            onAccept: function(sip){
                console.log("Blind transfer Accepted");

                session.data.terminateby = "us";
                session.data.reasonCode = 202;
                session.data.reasonText = "Transfer";

                session.data.transfer[transferId].accept.complete = true;
                session.data.transfer[transferId].accept.disposition = sip.message.reasonPhrase;
                session.data.transfer[transferId].accept.eventTime = utcDateNow();

                // TODO: use lang pack
                $("#line-" + lineNum + "-msg").html("Call Blind Transferred (Accepted)");

                updateLineScroll(lineNum);

                session.bye().catch(function(error){
                    console.warn("Could not BYE after blind transfer:", error);
                });
                teardownSession(lineObj);
            },
            onReject:function(sip){
                console.warn("REFER rejected:", sip);

                session.data.transfer[transferId].accept.complete = false;
                session.data.transfer[transferId].accept.disposition = sip.message.reasonPhrase;
                session.data.transfer[transferId].accept.eventTime = utcDateNow();

                $("#line-" + lineNum + "-msg").html("Call Blind Failed!");

                updateLineScroll(lineNum);

                // Session should still be up, so just allow them to try again
            }
        }
    }
    console.log("REFER: ", dstNo + "@" + SipDomain);
    var referTo = SIP.UserAgent.makeURI("sip:"+ dstNo.replace(/#/g, "%23") + "@" + SipDomain);
    session.refer(referTo, transferOptions).catch(function(error){
        console.warn("Failed to REFER", error);
    });;

    $("#line-" + lineNum + "-msg").html(lang.call_blind_transfered);

    updateLineScroll(lineNum);
}
function AttendedTransfer(lineNum){
    var dstNo = $("#line-"+ lineNum +"-txt-FindTransferBuddy").val();
    if(EnableAlphanumericDial){
        dstNo = dstNo.replace(telAlphanumericRegEx, "").substring(0,MaxDidLength);
    }
    else {
        dstNo = dstNo.replace(telNumericRegEx, "").substring(0,MaxDidLength);
    }
    if(dstNo == ""){
        console.warn("Cannot transfer, no number");
        return;
    }

    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Null line or session");
        return;
    }
    var session = lineObj.SipSession;

    HidePopup();

    $("#line-"+ lineNum +"-txt-FindTransferBuddy").parent().hide();
    $("#line-"+ lineNum +"-btn-blind-transfer").hide();
    $("#line-"+ lineNum +"-btn-attended-transfer").hide();

    $("#line-"+ lineNum +"-btn-complete-attended-transfer").hide();
    $("#line-"+ lineNum +"-btn-cancel-attended-transfer").hide();
    $("#line-"+ lineNum +"-btn-terminate-attended-transfer").hide();


    var newCallStatus = $("#line-"+ lineNum +"-transfer-status");
    newCallStatus.html(lang.connecting);
    newCallStatus.show();

    if(!session.data.transfer) session.data.transfer = [];
    session.data.transfer.push({
        type: "Attended",
        to: dstNo,
        transferTime: utcDateNow(),
        disposition: "invite",
        dispositionTime: utcDateNow(),
        accept : {
            complete: null,
            eventTime: null,
            disposition: ""
        }
    });
    var transferId = session.data.transfer.length-1;

    updateLineScroll(lineNum);

    // SDP options
    var supportedConstraints = navigator.mediaDevices.getSupportedConstraints();
    var spdOptions = {
        earlyMedia: true,
        sessionDescriptionHandlerOptions: {
            constraints: {
                audio: { deviceId : "default" },
                video: false
            }
        }
    }
    if(session.data.AudioSourceDevice != "default"){
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.deviceId = { exact: session.data.AudioSourceDevice }
    }
    // Add additional Constraints
    if(supportedConstraints.autoGainControl) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.autoGainControl = AutoGainControl;
    }
    if(supportedConstraints.echoCancellation) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.echoCancellation = EchoCancellation;
    }
    if(supportedConstraints.noiseSuppression) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.noiseSuppression = NoiseSuppression;
    }

    // Not sure if its possible to transfer a Video call???
    if(session.data.withvideo){
        spdOptions.sessionDescriptionHandlerOptions.constraints.video = true;
        if(session.data.VideoSourceDevice != "default"){
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.deviceId = { exact: session.data.VideoSourceDevice }
        }
        // Add additional Constraints
        if(supportedConstraints.frameRate && maxFrameRate != "") {
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.frameRate = maxFrameRate;
        }
        if(supportedConstraints.height && videoHeight != "") {
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.height = videoHeight;
        }
        if(supportedConstraints.aspectRatio && videoAspectRatio != "") {
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.aspectRatio = videoAspectRatio;
        }
    }

    // Create new call session
    console.log("TRANSFER INVITE: ", "sip:" + dstNo + "@" + SipDomain);
    var targetURI = SIP.UserAgent.makeURI("sip:"+ dstNo.replace(/#/g, "%23") + "@" + SipDomain);
    var newSession = new SIP.Inviter(userAgent, targetURI, spdOptions);
    newSession.data = {}
    newSession.delegate = {
        onBye: function(sip){
            console.log("New call session ended with BYE");
            newCallStatus.html(lang.call_ended);
            session.data.transfer[transferId].disposition = "bye";
            session.data.transfer[transferId].dispositionTime = utcDateNow();

            $("#line-"+ lineNum +"-txt-FindTransferBuddy").parent().show();
            $("#line-"+ lineNum +"-btn-blind-transfer").show();
            $("#line-"+ lineNum +"-btn-attended-transfer").show();

            $("#line-"+ lineNum +"-btn-complete-attended-transfer").hide();
            $("#line-"+ lineNum +"-btn-cancel-attended-transfer").hide();
            $("#line-"+ lineNum +"-btn-terminate-attended-transfer").hide();

            $("#line-"+ lineNum +"-msg").html(lang.attended_transfer_call_terminated);

            updateLineScroll(lineNum);

            window.setTimeout(function(){
                newCallStatus.hide();
                updateLineScroll(lineNum);
            }, 1000);
        },
        onSessionDescriptionHandler: function(sdh, provisional){
            if (sdh) {
                if(sdh.peerConnection){
                    sdh.peerConnection.ontrack = function(event){
                        var pc = sdh.peerConnection;

                        // Gets Remote Audio Track (Local audio is setup via initial GUM)
                        var remoteStream = new MediaStream();
                        pc.getReceivers().forEach(function (receiver) {
                            if(receiver.track && receiver.track.kind == "audio"){
                                remoteStream.addTrack(receiver.track);
                            }
                        });
                        var remoteAudio = $("#line-" + lineNum + "-transfer-remoteAudio").get(0);
                        remoteAudio.srcObject = remoteStream;
                        remoteAudio.onloadedmetadata = function(e) {
                            if (typeof remoteAudio.sinkId !== 'undefined') {
                                remoteAudio.setSinkId(session.data.AudioOutputDevice).then(function(){
                                    console.log("sinkId applied: "+ session.data.AudioOutputDevice);
                                }).catch(function(e){
                                    console.warn("Error using setSinkId: ", e);
                                });
                            }
                            remoteAudio.play();
                        }

                    }
                }
                else{
                    console.warn("onSessionDescriptionHandler fired without a peerConnection");
                }
            }
            else{
                console.warn("onSessionDescriptionHandler fired without a sessionDescriptionHandler");
            }
        }
    }
    session.data.childsession = newSession;
    var inviterOptions = {
        requestDelegate: {
            onTrying: function(sip){
                newCallStatus.html(lang.trying);
                session.data.transfer[transferId].disposition = "trying";
                session.data.transfer[transferId].dispositionTime = utcDateNow();

                $("#line-" + lineNum + "-msg").html(lang.attended_transfer_call_started);
            },
            onProgress:function(sip){
                newCallStatus.html(lang.ringing);
                session.data.transfer[transferId].disposition = "progress";
                session.data.transfer[transferId].dispositionTime = utcDateNow();

                $("#line-" + lineNum + "-msg").html(lang.attended_transfer_call_started);

                var CancelAttendedTransferBtn = $("#line-"+ lineNum +"-btn-cancel-attended-transfer");
                CancelAttendedTransferBtn.off('click');
                CancelAttendedTransferBtn.on('click', function(){
                    newSession.cancel().catch(function(error){
                        console.warn("Failed to CANCEL", error);
                    });
                    newCallStatus.html(lang.call_cancelled);
                    console.log("New call session canceled");

                    session.data.transfer[transferId].accept.complete = false;
                    session.data.transfer[transferId].accept.disposition = "cancel";
                    session.data.transfer[transferId].accept.eventTime = utcDateNow();

                    $("#line-" + lineNum + "-msg").html(lang.attended_transfer_call_cancelled);

                    updateLineScroll(lineNum);
                });
                CancelAttendedTransferBtn.show();

                updateLineScroll(lineNum);
            },
            onRedirect:function(sip){
                console.log("Redirect received:", sip);
            },
            onAccept:function(sip){
                newCallStatus.html(lang.call_in_progress);
                $("#line-"+ lineNum +"-btn-cancel-attended-transfer").hide();
                session.data.transfer[transferId].disposition = "accepted";
                session.data.transfer[transferId].dispositionTime = utcDateNow();

                var CompleteTransferBtn = $("#line-"+ lineNum +"-btn-complete-attended-transfer");
                CompleteTransferBtn.off('click');
                CompleteTransferBtn.on('click', function(){
                    var transferOptions  = {
                        requestDelegate: {
                            onAccept: function(sip){
                                console.log("Attended transfer Accepted");

                                session.data.terminateby = "us";
                                session.data.reasonCode = 202;
                                session.data.reasonText = "Attended Transfer";

                                session.data.transfer[transferId].accept.complete = true;
                                session.data.transfer[transferId].accept.disposition = sip.message.reasonPhrase;
                                session.data.transfer[transferId].accept.eventTime = utcDateNow();

                                $("#line-" + lineNum + "-msg").html(lang.attended_transfer_complete_accepted);

                                updateLineScroll(lineNum);

                                // We must end this session manually
                                session.bye().catch(function(error){
                                    console.warn("Could not BYE after blind transfer:", error);
                                });

                                teardownSession(lineObj);
                            },
                            onReject: function(sip){
                                console.warn("Attended transfer rejected:", sip);

                                session.data.transfer[transferId].accept.complete = false;
                                session.data.transfer[transferId].accept.disposition = sip.message.reasonPhrase;
                                session.data.transfer[transferId].accept.eventTime = utcDateNow();

                                $("#line-" + lineNum + "-msg").html("Attended Transfer Failed!");

                                updateLineScroll(lineNum);
                            }
                        }
                    }

                    // Send REFER
                    session.refer(newSession, transferOptions).catch(function(error){
                        console.warn("Failed to REFER", error);
                    });

                    newCallStatus.html(lang.attended_transfer_complete);

                    updateLineScroll(lineNum);
                });
                CompleteTransferBtn.show();

                updateLineScroll(lineNum);

                var TerminateAttendedTransferBtn = $("#line-"+ lineNum +"-btn-terminate-attended-transfer");
                TerminateAttendedTransferBtn.off('click');
                TerminateAttendedTransferBtn.on('click', function(){
                    newSession.bye().catch(function(error){
                        console.warn("Failed to BYE", error);
                    });
                    newCallStatus.html(lang.call_ended);
                    console.log("New call session end");

                    session.data.transfer[transferId].accept.complete = false;
                    session.data.transfer[transferId].accept.disposition = "bye";
                    session.data.transfer[transferId].accept.eventTime = utcDateNow();

                    $("#line-"+ lineNum +"-btn-complete-attended-transfer").hide();
                    $("#line-"+ lineNum +"-btn-cancel-attended-transfer").hide();
                    $("#line-"+ lineNum +"-btn-terminate-attended-transfer").hide();

                    $("#line-" + lineNum + "-msg").html(lang.attended_transfer_call_ended);

                    updateLineScroll(lineNum);

                    window.setTimeout(function(){
                        newCallStatus.hide();
                        CancelTransferSession(lineNum);
                        updateLineScroll(lineNum);
                    }, 1000);
                });
                TerminateAttendedTransferBtn.show();

                updateLineScroll(lineNum);
            },
            onReject:function(sip){
                console.log("New call session rejected: ", sip.message.reasonPhrase);
                newCallStatus.html(lang.call_rejected);
                session.data.transfer[transferId].disposition = sip.message.reasonPhrase;
                session.data.transfer[transferId].dispositionTime = utcDateNow();

                $("#line-"+ lineNum +"-txt-FindTransferBuddy").parent().show();
                $("#line-"+ lineNum +"-btn-blind-transfer").show();
                $("#line-"+ lineNum +"-btn-attended-transfer").show();

                $("#line-"+ lineNum +"-btn-complete-attended-transfer").hide();
                $("#line-"+ lineNum +"-btn-cancel-attended-transfer").hide();
                $("#line-"+ lineNum +"-btn-terminate-attended-transfer").hide();

                $("#line-"+ lineNum +"-msg").html(lang.attended_transfer_call_rejected);

                updateLineScroll(lineNum);

                window.setTimeout(function(){
                    newCallStatus.hide();
                    updateLineScroll(lineNum);
                }, 1000);
            }
        }
    }
    newSession.invite(inviterOptions).catch(function(e){
        console.warn("Failed to send INVITE:", e);
    });
}

// Conference Calls
// ================
function StartConferenceCall(lineNum){
    if($("#line-"+ lineNum +"-btn-CancelTransfer").is(":visible")){
        CancelTransferSession(lineNum);
        return;
    }

    $("#line-"+ lineNum +"-btn-Conference").hide();
    $("#line-"+ lineNum +"-btn-CancelConference").show();

    holdSession(lineNum);
    $("#line-"+ lineNum +"-txt-FindConferenceBuddy").val("");
    $("#line-"+ lineNum +"-txt-FindConferenceBuddy").parent().show();

    $("#line-"+ lineNum +"-session-avatar").css("width", "50px");
    $("#line-"+ lineNum +"-session-avatar").css("height", "50px");
    RestoreCallControls(lineNum)

    $("#line-"+ lineNum +"-btn-conference-dial").show();
    $("#line-"+ lineNum +"-btn-cancel-conference-dial").hide();
    $("#line-"+ lineNum +"-btn-join-conference-call").hide();
    $("#line-"+ lineNum +"-btn-terminate-conference-call").hide();

    $("#line-"+ lineNum +"-conference-status").hide();

    $("#line-"+ lineNum +"-Conference").show();

    updateLineScroll(lineNum);
}
function CancelConference(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Null line or session");
        return;
    }
    var session = lineObj.SipSession;
    if(session.data.childsession){
        console.log("Child Conference call detected:", session.data.childsession.state);
        session.data.childsession.dispose().then(function(){
            session.data.childsession = null;
        }).catch(function(error){
            session.data.childsession = null;
            // Suppress message
        });
    }

    $("#line-"+ lineNum +"-session-avatar").css("width", "");
    $("#line-"+ lineNum +"-session-avatar").css("height", "");

    $("#line-"+ lineNum +"-btn-Conference").show();
    $("#line-"+ lineNum +"-btn-CancelConference").hide();

    unholdSession(lineNum);
    $("#line-"+ lineNum +"-Conference").hide();

    updateLineScroll(lineNum);
}
function conferenceOnkeydown(event, obj, lineNum) {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13'){
        event.preventDefault();

        ConferenceDial(lineNum);
        return false;
    }
}
function ConferenceDial(lineNum){
    var dstNo = $("#line-"+ lineNum +"-txt-FindConferenceBuddy").val();
    if(EnableAlphanumericDial){
        dstNo = dstNo.replace(telAlphanumericRegEx, "").substring(0,MaxDidLength);
    }
    else {
        dstNo = dstNo.replace(telNumericRegEx, "").substring(0,MaxDidLength);
    }
    if(dstNo == ""){
        console.warn("Cannot transfer, must be [0-9*+#]");
        return;
    }

    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Null line or session");
        return;
    }
    var session = lineObj.SipSession;

    HidePopup();

    $("#line-"+ lineNum +"-txt-FindConferenceBuddy").parent().hide();

    $("#line-"+ lineNum +"-btn-conference-dial").hide();
    $("#line-"+ lineNum +"-btn-cancel-conference-dial")
    $("#line-"+ lineNum +"-btn-join-conference-call").hide();
    $("#line-"+ lineNum +"-btn-terminate-conference-call").hide();

    var newCallStatus = $("#line-"+ lineNum +"-conference-status");
    newCallStatus.html(lang.connecting);
    newCallStatus.show();

    if(!session.data.confcalls) session.data.confcalls = [];
    session.data.confcalls.push({
        to: dstNo,
        startTime: utcDateNow(),
        disposition: "invite",
        dispositionTime: utcDateNow(),
        accept : {
            complete: null,
            eventTime: null,
            disposition: ""
        }
    });
    var confCallId = session.data.confcalls.length-1;

    updateLineScroll(lineNum);

    // SDP options
    var supportedConstraints = navigator.mediaDevices.getSupportedConstraints();
    var spdOptions = {
        sessionDescriptionHandlerOptions: {
            earlyMedia: true,
            constraints: {
                audio: { deviceId : "default" },
                video: false
            }
        }
    }
    if(session.data.AudioSourceDevice != "default"){
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.deviceId = { exact: session.data.AudioSourceDevice }
    }
    // Add additional Constraints
    if(supportedConstraints.autoGainControl) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.autoGainControl = AutoGainControl;
    }
    if(supportedConstraints.echoCancellation) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.echoCancellation = EchoCancellation;
    }
    if(supportedConstraints.noiseSuppression) {
        spdOptions.sessionDescriptionHandlerOptions.constraints.audio.noiseSuppression = NoiseSuppression;
    }

    // Unlikely this will work
    if(session.data.withvideo){
        spdOptions.sessionDescriptionHandlerOptions.constraints.video = true;
        if(session.data.VideoSourceDevice != "default"){
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.deviceId = { exact: session.data.VideoSourceDevice }
        }
        // Add additional Constraints
        if(supportedConstraints.frameRate && maxFrameRate != "") {
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.frameRate = maxFrameRate;
        }
        if(supportedConstraints.height && videoHeight != "") {
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.height = videoHeight;
        }
        if(supportedConstraints.aspectRatio && videoAspectRatio != "") {
            spdOptions.sessionDescriptionHandlerOptions.constraints.video.aspectRatio = videoAspectRatio;
        }
    }

    // Create new call session
    console.log("CONFERENCE INVITE: ", "sip:" + dstNo + "@" + SipDomain);

    var targetURI = SIP.UserAgent.makeURI("sip:"+ dstNo.replace(/#/g, "%23") + "@" + SipDomain);
    var newSession = new SIP.Inviter(userAgent, targetURI, spdOptions);
    newSession.data = {}
    newSession.delegate = {
        onBye: function(sip){
            console.log("New call session ended with BYE");
            newCallStatus.html(lang.call_ended);
            session.data.confcalls[confCallId].disposition = "bye";
            session.data.confcalls[confCallId].dispositionTime = utcDateNow();

            $("#line-"+ lineNum +"-txt-FindConferenceBuddy").parent().show();
            $("#line-"+ lineNum +"-btn-conference-dial").show();

            $("#line-"+ lineNum +"-btn-cancel-conference-dial").hide();
            $("#line-"+ lineNum +"-btn-join-conference-call").hide();
            $("#line-"+ lineNum +"-btn-terminate-conference-call").hide();

            $("#line-"+ lineNum +"-msg").html(lang.conference_call_terminated);

            updateLineScroll(lineNum);

            window.setTimeout(function(){
                newCallStatus.hide();
                updateLineScroll(lineNum);
            }, 1000);
        },
        onSessionDescriptionHandler: function(sdh, provisional){
            if (sdh) {
                if(sdh.peerConnection){
                    sdh.peerConnection.ontrack = function(event){
                        var pc = sdh.peerConnection;

                        // Gets Remote Audio Track (Local audio is setup via initial GUM)
                        var remoteStream = new MediaStream();
                        pc.getReceivers().forEach(function (receiver) {
                            if(receiver.track && receiver.track.kind == "audio"){
                                remoteStream.addTrack(receiver.track);
                            }
                        });
                        var remoteAudio = $("#line-" + lineNum + "-conference-remoteAudio").get(0);
                        remoteAudio.srcObject = remoteStream;
                        remoteAudio.onloadedmetadata = function(e) {
                            if (typeof remoteAudio.sinkId !== 'undefined') {
                                remoteAudio.setSinkId(session.data.AudioOutputDevice).then(function(){
                                    console.log("sinkId applied: "+ session.data.AudioOutputDevice);
                                }).catch(function(e){
                                    console.warn("Error using setSinkId: ", e);
                                });
                            }
                            remoteAudio.play();
                        }
                    }
                }
                else{
                    console.warn("onSessionDescriptionHandler fired without a peerConnection");
                }
            }
            else{
                console.warn("onSessionDescriptionHandler fired without a sessionDescriptionHandler");
            }
        }
    }
    // Make sure we always restore audio paths
    newSession.stateChange.addListener(function(newState){
        if (newState == SIP.SessionState.Terminated) {
            // Ends the mixed audio, and releases the mic
            if(session.data.childsession.data.AudioSourceTrack && session.data.childsession.data.AudioSourceTrack.kind == "audio"){
                session.data.childsession.data.AudioSourceTrack.stop();
            }
            // Restore Audio Stream as it was changed
            if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
                var pc = session.sessionDescriptionHandler.peerConnection;
                pc.getSenders().forEach(function (RTCRtpSender) {
                    if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                        RTCRtpSender.replaceTrack(session.data.AudioSourceTrack).then(function(){
                            if(session.data.ismute){
                                RTCRtpSender.track.enabled = false;
                            }
                            else {
                                RTCRtpSender.track.enabled = true;
                            }
                        }).catch(function(){
                            console.error(e);
                        });
                        session.data.AudioSourceTrack = null;
                    }
                });
            }
        }
    });
    session.data.childsession = newSession;
    var inviterOptions = {
        requestDelegate: {
            onTrying: function(sip){
                newCallStatus.html(lang.ringing);
                session.data.confcalls[confCallId].disposition = "trying";
                session.data.confcalls[confCallId].dispositionTime = utcDateNow();

                $("#line-" + lineNum + "-msg").html(lang.conference_call_started);
            },
            onProgress:function(sip){
                newCallStatus.html(lang.ringing);
                session.data.confcalls[confCallId].disposition = "progress";
                session.data.confcalls[confCallId].dispositionTime = utcDateNow();

                $("#line-" + lineNum + "-msg").html(lang.conference_call_started);

                var CancelConferenceDialBtn = $("#line-"+ lineNum +"-btn-cancel-conference-dial");
                CancelConferenceDialBtn.off('click');
                CancelConferenceDialBtn.on('click', function(){
                    newSession.cancel().catch(function(error){
                        console.warn("Failed to CANCEL", error);
                    });
                    newCallStatus.html(lang.call_cancelled);
                    console.log("New call session canceled");

                    session.data.confcalls[confCallId].accept.complete = false;
                    session.data.confcalls[confCallId].accept.disposition = "cancel";
                    session.data.confcalls[confCallId].accept.eventTime = utcDateNow();

                    $("#line-" + lineNum + "-msg").html(lang.conference_call_cancelled);

                    updateLineScroll(lineNum);
                });
                CancelConferenceDialBtn.show();

                updateLineScroll(lineNum);
            },
            onRedirect:function(sip){
                console.log("Redirect received:", sip);
            },
            onAccept:function(sip){
                newCallStatus.html(lang.call_in_progress);
                $("#line-"+ lineNum +"-btn-cancel-conference-dial").hide();
                session.data.confcalls[confCallId].complete = true;
                session.data.confcalls[confCallId].disposition = "accepted";
                session.data.confcalls[confCallId].dispositionTime = utcDateNow();

                // Join Call
                var JoinCallBtn = $("#line-"+ lineNum +"-btn-join-conference-call");
                JoinCallBtn.off('click');
                JoinCallBtn.on('click', function(){
                    // Merge Call Audio
                    if(!session.data.childsession){
                        console.warn("Conference session lost");
                        return;
                    }

                    var outputStreamForSession = new MediaStream();
                    var outputStreamForConfSession = new MediaStream();

                    var pc = session.sessionDescriptionHandler.peerConnection;
                    var confPc = session.data.childsession.sessionDescriptionHandler.peerConnection;

                    // Get conf call input channel
                    confPc.getReceivers().forEach(function (RTCRtpReceiver) {
                        if(RTCRtpReceiver.track && RTCRtpReceiver.track.kind == "audio") {
                            console.log("Adding conference session:", RTCRtpReceiver.track.label);
                            outputStreamForSession.addTrack(RTCRtpReceiver.track);
                        }
                    });

                    // Get session input channel
                    pc.getReceivers().forEach(function (RTCRtpReceiver) {
                        if(RTCRtpReceiver.track && RTCRtpReceiver.track.kind == "audio") {
                            console.log("Adding conference session:", RTCRtpReceiver.track.label);
                            outputStreamForConfSession.addTrack(RTCRtpReceiver.track);
                        }
                    });

                    // Replace tracks of Parent Call
                    pc.getSenders().forEach(function (RTCRtpSender) {
                        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                            console.log("Switching to mixed Audio track on session");

                            session.data.AudioSourceTrack = RTCRtpSender.track;
                            outputStreamForSession.addTrack(RTCRtpSender.track);
                            var mixedAudioTrack = MixAudioStreams(outputStreamForSession).getAudioTracks()[0];
                            mixedAudioTrack.IsMixedTrack = true;

                            RTCRtpSender.replaceTrack(mixedAudioTrack);
                        }
                    });
                    // Replace tracks of Child Call
                    confPc.getSenders().forEach(function (RTCRtpSender) {
                        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                            console.log("Switching to mixed Audio track on conf call");

                            session.data.childsession.data.AudioSourceTrack = RTCRtpSender.track;
                            outputStreamForConfSession.addTrack(RTCRtpSender.track);
                            var mixedAudioTrackForConf = MixAudioStreams(outputStreamForConfSession).getAudioTracks()[0];
                            mixedAudioTrackForConf.IsMixedTrack = true;

                            RTCRtpSender.replaceTrack(mixedAudioTrackForConf);
                        }
                    });

                    newCallStatus.html(lang.call_in_progress);
                    console.log("Conference Call In Progress");

                    session.data.confcalls[confCallId].accept.complete = true;
                    session.data.confcalls[confCallId].accept.disposition = "join";
                    session.data.confcalls[confCallId].accept.eventTime = utcDateNow();

                    $("#line-"+ lineNum +"-btn-terminate-conference-call").show();

                    $("#line-" + lineNum + "-msg").html(lang.conference_call_in_progress);

                    JoinCallBtn.hide();
                    updateLineScroll(lineNum);

                    // Take the parent call off hold after a second
                    window.setTimeout(function(){
                        unholdSession(lineNum);
                        updateLineScroll(lineNum);
                    }, 1000);
                });
                JoinCallBtn.show();

                updateLineScroll(lineNum);

                // End Call
                var TerminateConfCallBtn = $("#line-"+ lineNum +"-btn-terminate-conference-call");
                TerminateConfCallBtn.off('click');
                TerminateConfCallBtn.on('click', function(){
                    newSession.bye().catch(function(e){
                        console.warn("Failed to BYE", e);
                    });
                    newCallStatus.html(lang.call_ended);
                    console.log("New call session end");

                    // session.data.confcalls[confCallId].accept.complete = false;
                    session.data.confcalls[confCallId].accept.disposition = "bye";
                    session.data.confcalls[confCallId].accept.eventTime = utcDateNow();

                    $("#line-" + lineNum + "-msg").html(lang.conference_call_ended);

                    updateLineScroll(lineNum);

                    window.setTimeout(function(){
                        newCallStatus.hide();
                        CancelConference(lineNum);
                        updateLineScroll(lineNum);
                    }, 1000);
                });
                TerminateConfCallBtn.show();

                updateLineScroll(lineNum);
            },
            onReject:function(sip){
                console.log("New call session rejected: ", sip.message.reasonPhrase);
                newCallStatus.html(lang.call_rejected);
                session.data.confcalls[confCallId].disposition = sip.message.reasonPhrase;
                session.data.confcalls[confCallId].dispositionTime = utcDateNow();

                $("#line-"+ lineNum +"-txt-FindConferenceBuddy").parent().show();
                $("#line-"+ lineNum +"-btn-conference-dial").show();

                $("#line-"+ lineNum +"-btn-cancel-conference-dial").hide();
                $("#line-"+ lineNum +"-btn-join-conference-call").hide();
                $("#line-"+ lineNum +"-btn-terminate-conference-call").hide();

                $("#line-"+ lineNum +"-msg").html(lang.conference_call_rejected);

                updateLineScroll(lineNum);

                window.setTimeout(function(){
                    newCallStatus.hide();
                    updateLineScroll(lineNum);
                }, 1000);
            }
        }
    }
    newSession.invite(inviterOptions).catch(function(e){
        console.warn("Failed to send INVITE:", e);
    });
}

// In-Session Call Functionality
// =============================

function cancelSession(lineNum) {
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;

    lineObj.SipSession.data.terminateby = "us";
    lineObj.SipSession.data.reasonCode = 0;
    lineObj.SipSession.data.reasonText = "Call Cancelled";

    console.log("Cancelling session : "+ lineNum);
    if(lineObj.SipSession.state == SIP.SessionState.Initial || lineObj.SipSession.state == SIP.SessionState.Establishing){
        lineObj.SipSession.cancel();
    }
    else {
        console.warn("Session not in correct state for cancel.", lineObj.SipSession.state);
        console.log("Attempting teardown : "+ lineNum);
        teardownSession(lineObj);
    }

    $("#line-" + lineNum + "-msg").html(lang.call_cancelled);
}
function holdSession(lineNum) {
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;
    var session = lineObj.SipSession;
    if(session.isOnHold == true) {
        console.log("Call is is already on hold:", lineNum);
        return;
    }
    console.log("Putting Call on hold:", lineNum);
    session.isOnHold = true;

    var sessionDescriptionHandlerOptions = session.sessionDescriptionHandlerOptionsReInvite;
    sessionDescriptionHandlerOptions.hold = true;
    session.sessionDescriptionHandlerOptionsReInvite = sessionDescriptionHandlerOptions;

    var options = {
        requestDelegate: {
            onAccept: function(){
                if(session && session.sessionDescriptionHandler && session.sessionDescriptionHandler.peerConnection){
                    var pc = session.sessionDescriptionHandler.peerConnection;
                    // Stop all the inbound streams
                    pc.getReceivers().forEach(function(RTCRtpReceiver){
                        if (RTCRtpReceiver.track) RTCRtpReceiver.track.enabled = false;
                    });
                    // Stop all the outbound streams (especially useful for Conference Calls!!)
                    pc.getSenders().forEach(function(RTCRtpSender){
                        // Mute Audio
                        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                            if(RTCRtpSender.track.IsMixedTrack == true){
                                if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
                                    console.log("Muting Mixed Audio Track : "+ session.data.AudioSourceTrack.label);
                                    session.data.AudioSourceTrack.enabled = false;
                                }
                            }
                            console.log("Muting Audio Track : "+ RTCRtpSender.track.label);
                            RTCRtpSender.track.enabled = false;
                        }
                        // Stop Video
                        else if(RTCRtpSender.track && RTCRtpSender.track.kind == "video"){
                            RTCRtpSender.track.enabled = false;
                        }
                    });
                }
                session.isOnHold = true;
                console.log("Call is is on hold:", lineNum);

                $("#line-" + lineNum + "-btn-Hold").hide();
                $("#line-" + lineNum + "-btn-Unhold").show();
                $("#line-" + lineNum + "-msg").html(lang.call_on_hold);

                // Log Hold
                if(!session.data.hold) session.data.hold = [];
                session.data.hold.push({ event: "hold", eventTime: utcDateNow() });

                updateLineScroll(lineNum);

                // Custom Web hook
                if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("hold", session);
            },
            onReject: function(){
                session.isOnHold = false;
                console.warn("Failed to put the call on hold:", lineNum);
            }
        }
    };
    session.invite(options).catch(function(error){
        session.isOnHold = false;
        console.warn("Error attempting to put the call on hold:", error);
    });
}
function unholdSession(lineNum) {
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;
    var session = lineObj.SipSession;
    if(session.isOnHold == false) {
        console.log("Call is already off hold:", lineNum);
        return;
    }
    console.log("Taking call off hold:", lineNum);
    session.isOnHold = false;

    var sessionDescriptionHandlerOptions = session.sessionDescriptionHandlerOptionsReInvite;
    sessionDescriptionHandlerOptions.hold = false;
    session.sessionDescriptionHandlerOptionsReInvite = sessionDescriptionHandlerOptions;

    var options = {
        requestDelegate: {
            onAccept: function(){
                if(session && session.sessionDescriptionHandler && session.sessionDescriptionHandler.peerConnection){
                    var pc = session.sessionDescriptionHandler.peerConnection;
                    // Restore all the inbound streams
                    pc.getReceivers().forEach(function(RTCRtpReceiver){
                        if (RTCRtpReceiver.track) RTCRtpReceiver.track.enabled = true;
                    });
                    // Restore all the outbound streams
                    pc.getSenders().forEach(function(RTCRtpSender){
                        // Unmute Audio
                        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                            if(RTCRtpSender.track.IsMixedTrack == true){
                                if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
                                    console.log("Unmuting Mixed Audio Track : "+ session.data.AudioSourceTrack.label);
                                    session.data.AudioSourceTrack.enabled = true;
                                }
                            }
                            console.log("Unmuting Audio Track : "+ RTCRtpSender.track.label);
                            RTCRtpSender.track.enabled = true;
                        }
                        else if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
                            RTCRtpSender.track.enabled = true;
                        }
                    });
                }
                session.isOnHold = false;
                console.log("Call is off hold:", lineNum);

                $("#line-" + lineNum + "-btn-Hold").show();
                $("#line-" + lineNum + "-btn-Unhold").hide();
                $("#line-" + lineNum + "-msg").html(lang.call_in_progress);

                // Log Hold
                if(!session.data.hold) session.data.hold = [];
                session.data.hold.push({ event: "unhold", eventTime: utcDateNow() });

                updateLineScroll(lineNum);

                // Custom Web hook
                if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("unhold", session);
            },
            onReject: function(){
                session.isOnHold = true;
                console.warn("Failed to put the call on hold", lineNum);
            }
        }
    };
    session.invite(options).catch(function(error){
        session.isOnHold = true;
        console.warn("Error attempting to take to call off hold", error);
    });
}
function MuteSession(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;

    $("#line-"+ lineNum +"-btn-Unmute").show();
    $("#line-"+ lineNum +"-btn-Mute").hide();

    var session = lineObj.SipSession;
    var pc = session.sessionDescriptionHandler.peerConnection;
    pc.getSenders().forEach(function (RTCRtpSender) {
        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
            if(RTCRtpSender.track.IsMixedTrack == true){
                if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
                    console.log("Muting Mixed Audio Track : "+ session.data.AudioSourceTrack.label);
                    session.data.AudioSourceTrack.enabled = false;
                }
            }
            console.log("Muting Audio Track : "+ RTCRtpSender.track.label);
            RTCRtpSender.track.enabled = false;
        }
    });

    if(!session.data.mute) session.data.mute = [];
    session.data.mute.push({ event: "mute", eventTime: utcDateNow() });
    session.data.ismute = true;

    $("#line-" + lineNum + "-msg").html(lang.call_on_mute);

    updateLineScroll(lineNum);

    // Custom Web hook
    if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("mute", session);
}
function UnmuteSession(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;

    $("#line-"+ lineNum +"-btn-Unmute").hide();
    $("#line-"+ lineNum +"-btn-Mute").show();

    var session = lineObj.SipSession;
    var pc = session.sessionDescriptionHandler.peerConnection;
    pc.getSenders().forEach(function (RTCRtpSender) {
        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
            if(RTCRtpSender.track.IsMixedTrack == true){
                if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
                    console.log("Unmuting Mixed Audio Track : "+ session.data.AudioSourceTrack.label);
                    session.data.AudioSourceTrack.enabled = true;
                }
            }
            console.log("Unmuting Audio Track : "+ RTCRtpSender.track.label);
            RTCRtpSender.track.enabled = true;
        }
    });

    if(!session.data.mute) session.data.mute = [];
    session.data.mute.push({ event: "unmute", eventTime: utcDateNow() });
    session.data.ismute = false;

    $("#line-" + lineNum + "-msg").html(lang.call_off_mute);

    updateLineScroll(lineNum);

    // Custom Web hook
    if(typeof web_hook_on_modify !== 'undefined') web_hook_on_modify("unmute", session);
}
function endSession(lineNum) {
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;

    console.log("Ending call with: "+ lineNum);
    lineObj.SipSession.data.terminateby = "us";
    lineObj.SipSession.data.reasonCode = 16;
    lineObj.SipSession.data.reasonText = "Normal Call clearing";

    lineObj.SipSession.bye().catch(function(e){
        console.warn("Failed to bye the session!", e);
    });

    $("#line-" + lineNum + "-msg").html(lang.call_ended);
    $("#line-" + lineNum + "-ActiveCall").hide();

    teardownSession(lineObj);

    updateLineScroll(lineNum);
}
function sendDTMF(lineNum, itemStr) {
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) return;

    // https://developer.mozilla.org/en-US/docs/Web/API/RTCDTMFSender/insertDTMF
    var options = {
        duration: 100,
        interToneGap: 70
    }

    if(lineObj.SipSession.isOnHold == true){
        if(lineObj.SipSession.data.childsession){
            if(lineObj.SipSession.data.childsession.state == SIP.SessionState.Established){
                console.log("Sending DTMF ("+ itemStr +"): "+ lineObj.LineNumber + " child session");

                var result = lineObj.SipSession.data.childsession.sessionDescriptionHandler.sendDtmf(itemStr, options);
                if(result){
                    console.log("Sent DTMF ("+ itemStr +") child session");
                }
                else{
                    console.log("Failed to send DTMF ("+ itemStr +") child session");
                }
            }
            else {
                console.warn("Cannot Send DTMF ("+ itemStr +"): "+ lineObj.LineNumber + " is on hold, and the child session is not established");
            }
        }
        else {
            console.warn("Cannot Send DTMF ("+ itemStr +"): "+ lineObj.LineNumber + " is on hold, and there is no child session");
        }
    }
    else {
        if(lineObj.SipSession.state == SIP.SessionState.Established || lineObj.SipSession.state == SIP.SessionState.Establishing){
            console.log("Sending DTMF ("+ itemStr +"): "+ lineObj.LineNumber);

            var result = lineObj.SipSession.sessionDescriptionHandler.sendDtmf(itemStr, options);
            if(result){
                console.log("Sent DTMF ("+ itemStr +")");
            }
            else{
                console.log("Failed to send DTMF ("+ itemStr +")");
            }

            $("#line-" + lineNum + "-msg").html(lang.send_dtmf + ": "+ itemStr);

            updateLineScroll(lineNum);

            // Custom Web hook
            if(typeof web_hook_on_dtmf !== 'undefined') web_hook_on_dtmf(itemStr, lineObj.SipSession);
        }
        else {
            console.warn("Cannot Send DTMF ("+ itemStr +"): "+ lineObj.LineNumber + " session is not establishing or established");
        }
    }
}
function switchVideoSource(lineNum, srcId){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null");
        return;
    }
    var session = lineObj.SipSession;

    $("#line-" + lineNum + "-msg").html(lang.switching_video_source);

    var supportedConstraints = navigator.mediaDevices.getSupportedConstraints();
    var constraints = {
        audio: false,
        video: { deviceId: "default" }
    }
    if(srcId != "default"){
        constraints.video.deviceId = { exact: srcId }
    }

    // Add additional Constraints
    if(supportedConstraints.frameRate && maxFrameRate != "") {
        constraints.video.frameRate = maxFrameRate;
    }
    if(supportedConstraints.height && videoHeight != "") {
        constraints.video.height = videoHeight;
    }
    if(supportedConstraints.aspectRatio && videoAspectRatio != "") {
        constraints.video.aspectRatio = videoAspectRatio;
    }

    session.data.VideoSourceDevice = srcId;

    var pc = session.sessionDescriptionHandler.peerConnection;

    var localStream = new MediaStream();
    navigator.mediaDevices.getUserMedia(constraints).then(function(newStream){
        var newMediaTrack = newStream.getVideoTracks()[0];
        // var pc = session.sessionDescriptionHandler.peerConnection;
        pc.getSenders().forEach(function (RTCRtpSender) {
            if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
                console.log("Switching Video Track : "+ RTCRtpSender.track.label + " to "+ newMediaTrack.label);
                RTCRtpSender.track.stop();
                RTCRtpSender.replaceTrack(newMediaTrack);
                localStream.addTrack(newMediaTrack);
            }
        });
    }).catch(function(e){
        console.error("Error on getUserMedia", e, constraints);
    });

    // Restore Audio Stream is it was changed
    if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
        pc.getSenders().forEach(function (RTCRtpSender) {
            if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                RTCRtpSender.replaceTrack(session.data.AudioSourceTrack).then(function(){
                    if(session.data.ismute){
                        RTCRtpSender.track.enabled = false;
                    }
                    else {
                        RTCRtpSender.track.enabled = true;
                    }
                }).catch(function(){
                    console.error(e);
                });
                session.data.AudioSourceTrack = null;
            }
        });
    }

    // Set Preview
    console.log("Showing as preview...");
    var localVideo = $("#line-" + lineNum + "-localVideo").get(0);
    localVideo.srcObject = localStream;
    localVideo.onloadedmetadata = function(e) {
        localVideo.play();
    }
}
function SendCanvas(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null");
        return;
    }
    var session = lineObj.SipSession;

    $("#line-" + lineNum + "-msg").html(lang.switching_to_canvas);

    // Create scratch Pad
    RemoveScratchpad(lineNum);

    // TODO: This needs work!
    var newCanvas = $('<canvas/>');
    newCanvas.prop("id", "line-" + lineNum + "-scratchpad");
    $("#line-" + lineNum + "-scratchpad-container").append(newCanvas);
    $("#line-" + lineNum + "-scratchpad").css("display", "inline-block");
    $("#line-" + lineNum + "-scratchpad").css("width", "100%"); // SD
    $("#line-" + lineNum + "-scratchpad").css("height", "100%"); // SD
    $("#line-" + lineNum + "-scratchpad").prop("width", 640); // SD
    $("#line-" + lineNum + "-scratchpad").prop("height", 360); // SD
    $("#line-" + lineNum + "-scratchpad-container").show();

    console.log("Canvas for Scratchpad created...");

    scratchpad = new fabric.Canvas("line-" + lineNum + "-scratchpad");
    scratchpad.id = "line-" + lineNum + "-scratchpad";
    scratchpad.backgroundColor = "#FFFFFF";
    scratchpad.isDrawingMode = true;
    scratchpad.renderAll();
    scratchpad.redrawIntrtval = window.setInterval(function(){
        scratchpad.renderAll();
    }, 1000);

    CanvasCollection.push(scratchpad);

    // Get The Canvas Stream
    var canvasMediaStream = $("#line-"+ lineNum +"-scratchpad").get(0).captureStream(25);
    var canvasMediaTrack = canvasMediaStream.getVideoTracks()[0];

    // Switch Tracks
    var pc = session.sessionDescriptionHandler.peerConnection;
    pc.getSenders().forEach(function (RTCRtpSender) {
        if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
            console.log("Switching Track : "+ RTCRtpSender.track.label + " to Scratchpad Canvas");
            RTCRtpSender.track.stop();
            RTCRtpSender.replaceTrack(canvasMediaTrack);
        }
    });

    // Restore Audio Stream is it was changed
    if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
        pc.getSenders().forEach(function (RTCRtpSender) {
            if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                RTCRtpSender.replaceTrack(session.data.AudioSourceTrack).then(function(){
                    if(session.data.ismute){
                        RTCRtpSender.track.enabled = false;
                    }
                    else {
                        RTCRtpSender.track.enabled = true;
                    }
                }).catch(function(){
                    console.error(e);
                });
                session.data.AudioSourceTrack = null;
            }
        });
    }

    // Set Preview
    // ===========
    console.log("Showing as preview...");
    var localVideo = $("#line-" + lineNum + "-localVideo").get(0);
    localVideo.srcObject = canvasMediaStream;
    localVideo.onloadedmetadata = function(e) {
        localVideo.play();
    }
}
function SendVideo(lineNum, src){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null");
        return;
    }

    var session = lineObj.SipSession;

    $("#line-"+ lineNum +"-src-camera").prop("disabled", false);
    $("#line-"+ lineNum +"-src-canvas").prop("disabled", false);
    $("#line-"+ lineNum +"-src-desktop").prop("disabled", false);
    $("#line-"+ lineNum +"-src-video").prop("disabled", true);
    $("#line-"+ lineNum +"-src-blank").prop("disabled", false);

    $("#line-" + lineNum + "-msg").html(lang.switching_to_shared_video);

    $("#line-" + lineNum + "-scratchpad-container").hide();
    RemoveScratchpad(lineNum);
    $("#line-"+ lineNum +"-sharevideo").hide();
    $("#line-"+ lineNum +"-sharevideo").get(0).pause();
    $("#line-"+ lineNum +"-sharevideo").get(0).removeAttribute('src');
    $("#line-"+ lineNum +"-sharevideo").get(0).load();

    $("#line-"+ lineNum +"-localVideo").hide();
    $("#line-"+ lineNum +"-remote-videos").hide();
    // $("#line-"+ lineNum +"-remoteVideo").appendTo("#line-" + lineNum + "-preview-container");

    // Create Video Object
    var newVideo = $("#line-" + lineNum + "-sharevideo");
    newVideo.prop("src", src);
    newVideo.off("loadedmetadata");
    newVideo.on("loadedmetadata", function () {
        console.log("Video can play now... ");

        // Resample Video
        var ResampleSize = 360;
        if(VideoResampleSize == "HD") ResampleSize = 720;
        if(VideoResampleSize == "FHD") ResampleSize = 1080;

        var videoObj = newVideo.get(0);
        var resampleCanvas = $('<canvas/>').get(0);

        var videoWidth = videoObj.videoWidth;
        var videoHeight = videoObj.videoHeight;
        if(videoWidth >= videoHeight){
            // Landscape / Square
            if(videoHeight > ResampleSize){
                var p = ResampleSize / videoHeight;
                videoHeight = ResampleSize;
                videoWidth = videoWidth * p;
            }
        }
        else {
            // Portrait... (phone turned on its side)
            if(videoWidth > ResampleSize){
                var p = ResampleSize / videoWidth;
                videoWidth = ResampleSize;
                videoHeight = videoHeight * p;
            }
        }

        resampleCanvas.width = videoWidth;
        resampleCanvas.height = videoHeight;
        var resampleContext = resampleCanvas.getContext("2d");

        window.clearInterval(session.data.videoResampleInterval);
        session.data.videoResampleInterval = window.setInterval(function(){
            resampleContext.drawImage(videoObj, 0, 0, videoWidth, videoHeight);
        }, 40); // 25frames per second

        // Capture the streams
        var videoMediaStream = null;
        if('captureStream' in videoObj) {
            videoMediaStream = videoObj.captureStream();
        }
        else if('mozCaptureStream' in videoObj) {
            // This doesn't really work?
            // see: https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/captureStream
            videoMediaStream = videoObj.mozCaptureStream();
        }
        else {
            // This is not supported??.
            // videoMediaStream = videoObj.webkitCaptureStream();
            console.warn("Cannot capture stream from video, this will result in no audio being transmitted.")
        }
        var resampleVideoMediaStream = resampleCanvas.captureStream(25);

        // Get the Tracks
        var videoMediaTrack = resampleVideoMediaStream.getVideoTracks()[0];
        var audioTrackFromVideo = (videoMediaStream != null )? videoMediaStream.getAudioTracks()[0] : null;

        // Switch & Merge Tracks
        var pc = session.sessionDescriptionHandler.peerConnection;
        pc.getSenders().forEach(function (RTCRtpSender) {
            if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
                console.log("Switching Track : "+ RTCRtpSender.track.label);
                RTCRtpSender.track.stop();
                RTCRtpSender.replaceTrack(videoMediaTrack);
            }
            if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                console.log("Switching to mixed Audio track on session");

                session.data.AudioSourceTrack = RTCRtpSender.track;

                var mixedAudioStream = new MediaStream();
                if(audioTrackFromVideo) mixedAudioStream.addTrack(audioTrackFromVideo);
                mixedAudioStream.addTrack(RTCRtpSender.track);
                var mixedAudioTrack = MixAudioStreams(mixedAudioStream).getAudioTracks()[0];
                mixedAudioTrack.IsMixedTrack = true;

                RTCRtpSender.replaceTrack(mixedAudioTrack);
            }
        });

        // Set Preview
        console.log("Showing as preview...");
        var localVideo = $("#line-" + lineNum + "-localVideo").get(0);
        localVideo.srcObject = videoMediaStream;
        localVideo.onloadedmetadata = function(e) {
            localVideo.play().then(function(){
                console.log("Playing Preview Video File");
            }).catch(function(e){
                console.error("Cannot play back video", e);
            });
        }
        // Play the video
        console.log("Starting Video...");
        $("#line-"+ lineNum +"-sharevideo").get(0).play();
    });

    $("#line-"+ lineNum +"-sharevideo").show();
    console.log("Video for Sharing created...");
}
function ShareScreen(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null");
        return;
    }
    var session = lineObj.SipSession;

    $("#line-" + lineNum + "-msg").html(lang.switching_to_shared_screen);

    var localStream = new MediaStream();
    var pc = session.sessionDescriptionHandler.peerConnection;

    // TODO: Remove legacy ones
    if (navigator.getDisplayMedia) {
        // EDGE, legacy support
        var screenShareConstraints = { video: true, audio: false }
        navigator.getDisplayMedia(screenShareConstraints).then(function(newStream) {
            console.log("navigator.getDisplayMedia")
            var newMediaTrack = newStream.getVideoTracks()[0];
            pc.getSenders().forEach(function (RTCRtpSender) {
                if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
                    console.log("Switching Video Track : "+ RTCRtpSender.track.label + " to Screen");
                    RTCRtpSender.track.stop();
                    RTCRtpSender.replaceTrack(newMediaTrack);
                    localStream.addTrack(newMediaTrack);
                }
            });

            // Set Preview
            // ===========
            console.log("Showing as preview...");
            var localVideo = $("#line-" + lineNum + "-localVideo").get(0);
            localVideo.srcObject = localStream;
            localVideo.onloadedmetadata = function(e) {
                localVideo.play();
            }
        }).catch(function (err) {
            console.error("Error on getUserMedia");
        });
    }
    else if (navigator.mediaDevices.getDisplayMedia) {
        // New standard
        var screenShareConstraints = { video: true, audio: false }
        navigator.mediaDevices.getDisplayMedia(screenShareConstraints).then(function(newStream) {
            console.log("navigator.mediaDevices.getDisplayMedia")
            var newMediaTrack = newStream.getVideoTracks()[0];
            pc.getSenders().forEach(function (RTCRtpSender) {
                if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
                    console.log("Switching Video Track : "+ RTCRtpSender.track.label + " to Screen");
                    RTCRtpSender.track.stop();
                    RTCRtpSender.replaceTrack(newMediaTrack);
                    localStream.addTrack(newMediaTrack);
                }
            });

            // Set Preview
            // ===========
            console.log("Showing as preview...");
            var localVideo = $("#line-" + lineNum + "-localVideo").get(0);
            localVideo.srcObject = localStream;
            localVideo.onloadedmetadata = function(e) {
                localVideo.play();
            }
        }).catch(function (err) {
            console.error("Error on getUserMedia");
        });
    }
    else {
        // Firefox, apparently
        var screenShareConstraints = { video: { mediaSource: 'screen' }, audio: false }
        navigator.mediaDevices.getUserMedia(screenShareConstraints).then(function(newStream) {
            console.log("navigator.mediaDevices.getUserMedia")
            var newMediaTrack = newStream.getVideoTracks()[0];
            pc.getSenders().forEach(function (RTCRtpSender) {
                if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
                    console.log("Switching Video Track : "+ RTCRtpSender.track.label + " to Screen");
                    RTCRtpSender.track.stop();
                    RTCRtpSender.replaceTrack(newMediaTrack);
                    localStream.addTrack(newMediaTrack);
                }
            });

            // Set Preview
            console.log("Showing as preview...");
            var localVideo = $("#line-" + lineNum + "-localVideo").get(0);
            localVideo.srcObject = localStream;
            localVideo.onloadedmetadata = function(e) {
                localVideo.play();
            }
        }).catch(function (err) {
            console.error("Error on getUserMedia");
        });
    }

    // Restore Audio Stream is it was changed
    if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
        pc.getSenders().forEach(function (RTCRtpSender) {
            if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                RTCRtpSender.replaceTrack(session.data.AudioSourceTrack).then(function(){
                    if(session.data.ismute){
                        RTCRtpSender.track.enabled = false;
                    }
                    else {
                        RTCRtpSender.track.enabled = true;
                    }
                }).catch(function(){
                    console.error(e);
                });
                session.data.AudioSourceTrack = null;
            }
        });
    }

}
function DisableVideoStream(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null");
        return;
    }
    var session = lineObj.SipSession;

    var pc = session.sessionDescriptionHandler.peerConnection;
    pc.getSenders().forEach(function (RTCRtpSender) {
        if(RTCRtpSender.track && RTCRtpSender.track.kind == "video") {
            console.log("Disable Video Track : "+ RTCRtpSender.track.label + "");
            RTCRtpSender.track.enabled = false; //stop();
        }
        if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
            if(session.data.AudioSourceTrack && session.data.AudioSourceTrack.kind == "audio"){
                RTCRtpSender.replaceTrack(session.data.AudioSourceTrack).then(function(){
                    if(session.data.ismute){
                        RTCRtpSender.track.enabled = false;
                    }
                    else {
                        RTCRtpSender.track.enabled = true;
                    }
                }).catch(function(){
                    console.error(e);
                });
                session.data.AudioSourceTrack = null;
            }
        }
    });

    // Set Preview
    console.log("Showing as preview...");
    var localVideo = $("#line-" + lineNum + "-localVideo").get(0);
    localVideo.pause();
    localVideo.removeAttribute('src');
    localVideo.load();

    $("#line-" + lineNum + "-msg").html(lang.video_disabled);
}
function ShowDtmfMenu(lineNum){
    console.log("Show DTMF");
    HidePopup();

    RestoreCallControls(lineNum)

    // DTMF
    var html = ""
    html += "<div>";
    html += "<table cellspacing=10 cellpadding=0 style=\"margin-left:auto; margin-right: auto\">";
    html += "<tr><td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '1')\"><div>1</div><span>&nbsp;</span></button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '2')\"><div>2</div><span>ABC</span></button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '3')\"><div>3</div><span>DEF</span></button></td></tr>";
    html += "<tr><td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '4')\"><div>4</div><span>GHI</span></button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '5')\"><div>5</div><span>JKL</span></button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '6')\"><div>6</div><span>MNO</span></button></td></tr>";
    html += "<tr><td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '7')\"><div>7</div><span>PQRS</span></button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '8')\"><div>8</div><span>TUV</span></button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '9')\"><div>9</div><span>WXYZ</span></button></td></tr>";
    html += "<tr><td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '*')\">*</button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '0')\">0</button></td>"
    html += "<td><button class=dialButtons onclick=\"sendDTMF('"+ lineNum +"', '#')\">#</button></td></tr>";
    html += "</table>";
    html += "</div>";

    var h = 400;
    var w = 240
    OpenWindow(html, lang.send_dtmf, h, w, false, false, lang.cancel, function(){
        CloseWindow()
    });
}
function ShowPresentMenu(obj, lineNum){
    var items = [];
    items.push({value: "src-camera", icon : "fa fa-video-camera", text: lang.camera, isHeader: false }); // Camera
    items.push({value: "src-canvas", icon : "fa fa-pencil-square", text: lang.scratchpad, isHeader: false }); // Canvas
    items.push({value: "src-desktop", icon : "fa fa-desktop", text: lang.screen, isHeader: false }); // Screens
    items.push({value: "src-video", icon : "fa fa-file-video-o", text: lang.video, isHeader: false }); // Video
    items.push({value: "src-blank", icon : "fa fa-ban", text: lang.blank, isHeader: false }); // None

    var menu = {
        selectEvent : function( event, ui ) {
            var id = ui.item.attr("value");
            if(id != null) {
                if(id == "src-camera") PresentCamera(lineNum);
                if(id == "src-canvas") PresentScratchpad(lineNum);
                if(id == "src-desktop") PresentScreen(lineNum);
                if(id == "src-video") PresentVideo(lineNum);
                if(id == "src-blank") PresentBlank(lineNum);
                HidePopup();
            }
            else {
                HidePopup();
            }
        },
        createEvent : null,
        autoFocus : true,
        items : items
    }
    PopupMenu(obj, menu);
}

function ShowCallTimeline(lineNum){
    console.log("Show Timeline");
    HidePopup();
    RestoreCallControls(lineNum)

    if($("#line-"+ lineNum +"-AudioStats").is(":visible")){
        // The AudioStats is open, they can't take the same space
        HideCallStats(lineNum)
    }

    $("#line-"+ lineNum +"-AudioOrVideoCall").hide();
    $("#line-"+ lineNum +"-CallDetails").show();

    $("#line-"+ lineNum +"-btn-ShowTimeline").hide();
    $("#line-"+ lineNum +"-btn-HideTimeline").show();
}
function HideCallTimeline(lineNum){
    console.log("Hide Timeline");
    HidePopup();

    $("#line-"+ lineNum +"-CallDetails").hide();
    $("#line-"+ lineNum +"-AudioOrVideoCall").show();

    $("#line-"+ lineNum +"-btn-ShowTimeline").show();
    $("#line-"+ lineNum +"-btn-HideTimeline").hide();
}
function ShowCallStats(lineNum){
    console.log("Show Call Stats");
    HidePopup();
    RestoreCallControls(lineNum)

    if($("#line-"+ lineNum +"-CallDetails").is(":visible")){
        // The Timeline is open, they can't take the same space
        HideCallTimeline(lineNum)
    }

    $("#line-"+ lineNum +"-AudioOrVideoCall").hide();
    $("#line-"+ lineNum +"-AudioStats").show();

    $("#line-"+ lineNum +"-btn-ShowCallStats").hide();
    $("#line-"+ lineNum +"-btn-HideCallStats").show();
}
function HideCallStats(lineNum){
    console.log("Hide Call Stats");

    HidePopup();
    $("#line-"+ lineNum +"-AudioOrVideoCall").show();
    $("#line-"+ lineNum +"-AudioStats").hide();

    $("#line-"+ lineNum +"-btn-ShowCallStats").show();
    $("#line-"+ lineNum +"-btn-HideCallStats").hide();
}
function ToggleMoreButtons(lineNum){
    if($("#line-"+ lineNum +"-btn-more").is(":visible")){
        // The more buttons are showing, drop them down
        RestoreCallControls(lineNum);
    } else {
        ExpandCallControls(lineNum);
    }
}
function ExpandCallControls(lineNum){
    $("#line-"+ lineNum +"-btn-more").show(200);
    $("#line-"+ lineNum +"-btn-ControlToggle").html('<i class=\"fa fa-chevron-down\"></i>');
}
function RestoreCallControls(lineNum){
    $("#line-"+ lineNum +"-btn-more").hide(200);
    $("#line-"+ lineNum +"-btn-ControlToggle").html('<i class=\"fa fa-chevron-up\"></i>');
}
function ExpandVideoArea(lineNum){
    $("#line-" + lineNum + "-call-fullscreen").prop("class","streamSection highlightSection FullScreenVideo");

    $("#line-" + lineNum + "-btn-restore").show();
    $("#line-" + lineNum + "-btn-expand").hide();

    $("#line-" + lineNum + "-VideoCall").css("background-color", "#000000");

    RedrawStage(lineNum, false);
    if(typeof web_hook_on_expand_video_area !== 'undefined') {
        web_hook_on_expand_video_area(lineNum);
    }
}
function RestoreVideoArea(lineNum){
    $("#line-" + lineNum + "-call-fullscreen").prop("class","streamSection highlightSection");

    $("#line-" + lineNum + "-btn-restore").hide();
    $("#line-" + lineNum + "-btn-expand").show();

    $("#line-" + lineNum + "-VideoCall").css("background-color", "");

    RedrawStage(lineNum, false);
    if(typeof web_hook_on_restore_video_area !== 'undefined') {
        web_hook_on_restore_video_area(lineNum);
    }
}

// Phone Lines
// ===========
var Line = function(lineNumber, displayName, displayNumber, buddyObj){
    this.LineNumber = lineNumber;
    this.DisplayName = displayName;
    this.DisplayNumber = displayNumber;
    this.IsSelected = false;
    this.BuddyObj = buddyObj;
    this.SipSession = null;
    this.LocalSoundMeter = null;
    this.RemoteSoundMeter = null;
}

function handleDialInput(obj, event){
    if(EnableAlphanumericDial){
        $("#dialText").val($("#dialText").val().replace(/[^\da-zA-Z\*\#\+]/g, "").substring(0,MaxDidLength));
    }
    else {
        $("#dialText").val($("#dialText").val().replace(/[^\d\*\#\+]/g, "").substring(0,MaxDidLength));
    }
    $("#dialVideo").prop('disabled', ($("#dialText").val().length >= DidLength));
    if($("#dialText").val().length > 0){
        $("#dialText").css("width","138px");
        $("#dialDeleteKey").show();
    } else {
        $("#dialText").css("width","170px");
        $("#dialDeleteKey").hide();
    }
}

function CloseUpSettings(){
    // Video Preview
    try{
        settingsVideoStreamTrack.stop();
        console.log("settingsVideoStreamTrack... stopped");
    }
    catch(e){}
    try{
        var localVideo = $("#local-video-preview").get(0);
        localVideo.srcObject = null;
    }
    catch{}
    settingsVideoStream = null;

    // Microphone Preview
    try{
        settingsMicrophoneStreamTrack.stop();
        console.log("settingsMicrophoneStreamTrack... stopped");
    }
    catch(e){}
    settingsMicrophoneStream = null;

    // Microphone Meter
    try{
        settingsMicrophoneSoundMeter.stop();
    }
    catch(e){}
    settingsMicrophoneSoundMeter = null;

    // Speaker Preview
    try{
        window.SettingsOutputAudio.pause();
    }
    catch(e){}
    window.SettingsOutputAudio = null;

    try{
        var tracks = window.SettingsOutputStream.getTracks();
        tracks.forEach(function(track) {
            track.stop();
        });
    }
    catch(e){}
    window.SettingsOutputStream = null;

    try{
        var soundMeter = window.SettingsOutputStreamMeter;
        soundMeter.stop();
    }
    catch(e){}
    window.SettingsOutputStreamMeter = null;

    // Ringer Preview
    try{
        window.SettingsRingerAudio.pause();
    }
    catch(e){}
    window.SettingsRingerAudio = null;

    try{
        var tracks = window.SettingsRingerStream.getTracks();
        tracks.forEach(function(track) {
            track.stop();
        });
    }
    catch(e){}
    window.SettingsRingerStream = null;

    try{
        var soundMeter = window.SettingsRingerStreamMeter;
        soundMeter.stop();
    }
    catch(e){}
    window.SettingsRingerStreamMeter = null;
}
function ShowContacts(){

    CloseUpSettings()

    $("#actionArea").hide();
    $("#actionArea").empty();

    $("#myContacts").show();
    $("#searchArea").show();
}
function ShowSortAnfFilter(){
    ShowContacts();

    $("#myContacts").hide();
    $("#searchArea").hide();
    $("#actionArea").empty();

    var html = "<div style=\"text-align:right\"><button class=roundButtons onclick=\"ShowContacts()\"><i class=\"fa fa-close\"></i></button></div>"
    html += "<table cellspacing=10 cellpadding=0 style=\"margin-left:auto; margin-right: auto\">";
    // By Type (and what order)
    html += "<tr><td><div><input disabled type=radio name=sort_by id=sort_by_type><label for=sort_by_type>"+ lang.sort_type +"</label></div>";
    html += "<div style=\"margin-left:20px\"><input type=radio name=sort_by_type id=sort_by_type_cex><label for=sort_by_type_cex>"+ lang.sort_type_cex +"</label></div>";
    html += "<div style=\"margin-left:20px\"><input type=radio name=sort_by_type id=sort_by_type_cxe><label for=sort_by_type_cxe>"+ lang.sort_type_cxe +"</label></div>";
    html += "<div style=\"margin-left:20px\"><input type=radio name=sort_by_type id=sort_by_type_xec><label for=sort_by_type_xec>"+ lang.sort_type_xec +"</label></div>";
    html += "<div style=\"margin-left:20px\"><input type=radio name=sort_by_type id=sort_by_type_xce><label for=sort_by_type_xce>"+ lang.sort_type_xce +"</label></div>";
    html += "<div style=\"margin-left:20px\"><input type=radio name=sort_by_type id=sort_by_type_exc><label for=sort_by_type_exc>"+ lang.sort_type_exc +"</label></div>";
    html += "<div style=\"margin-left:20px\"><input type=radio name=sort_by_type id=sort_by_type_ecx><label for=sort_by_type_ecx>"+ lang.sort_type_ecx +"</label></div>";
    html += "</td></tr>";
    // By Extension
    html += "<tr><td><div><input type=radio name=sort_by id=sort_by_exten><label for=sort_by_exten>"+ lang.sort_exten +"</label></div></td></tr>";
    // By Alphabetical
    html += "<tr><td><div><input type=radio name=sort_by id=sort_by_alpha><label for=sort_by_alpha>"+ lang.sort_alpha +"</label></div></td></tr>";
    // Only Last Activity
    html += "<tr><td><div><input type=radio name=sort_by id=sort_by_activity><label for=sort_by_activity>"+ lang.sort_activity +"</label></div></td></tr>";

    // Secondary Options
    html += "<tr><td><div><input type=checkbox id=sort_auto_delete_at_end><label for=sort_auto_delete_at_end>"+ lang.sort_auto_delete_at_end +"</label></div></td></tr>";
    html += "<tr><td><div><input type=checkbox id=sort_auto_delete_hide><label for=sort_auto_delete_hide>"+ lang.sort_auto_delete_hide +"</label></div></td></tr>";
    html += "<tr><td><div><input type=checkbox id=sort_show_exten_num><label for=sort_show_exten_num>"+ lang.sort_show_exten_num +"</label></div></td></tr>";

    html += "</table>";
    html += "</div>";
    $("#actionArea").html(html);

    $("#sort_by_type").prop("checked", BuddySortBy=="type");
    $("#sort_by_type_cex").prop("checked", (BuddySortBy=="type" && SortByTypeOrder=="c|e|x"));
    $("#sort_by_type_cxe").prop("checked", (BuddySortBy=="type" && SortByTypeOrder=="c|x|e"));
    $("#sort_by_type_xec").prop("checked", (BuddySortBy=="type" && SortByTypeOrder=="x|e|c"));
    $("#sort_by_type_xce").prop("checked", (BuddySortBy=="type" && SortByTypeOrder=="x|c|e"));
    $("#sort_by_type_exc").prop("checked", (BuddySortBy=="type" && SortByTypeOrder=="e|x|c"));
    $("#sort_by_type_ecx").prop("checked", (BuddySortBy=="type" && SortByTypeOrder=="e|c|x"));
    $("#sort_by_exten").prop("checked", BuddySortBy=="extension");
    $("#sort_by_alpha").prop("checked", BuddySortBy=="alphabetical");
    $("#sort_by_activity").prop("checked", BuddySortBy=="activity");

    $("#sort_auto_delete_at_end").prop("checked", BuddyAutoDeleteAtEnd==true);
    $("#sort_auto_delete_hide").prop("checked", HideAutoDeleteBuddies==true);
    $("#sort_show_exten_num").prop("checked", BuddyShowExtenNum==true);

    $("#sort_by_type_cex").change(function(){
        BuddySortBy = "type";
        localDB.setItem("BuddySortBy", "type");
        SortByTypeOrder = "c|e|x"
        localDB.setItem("SortByTypeOrder", "c|e|x");
        $("#sort_by_type").prop("checked", true);

        UpdateBuddyList();
    });
    $("#sort_by_type_cxe").change(function(){
        BuddySortBy = "type";
        localDB.setItem("BuddySortBy", "type");
        SortByTypeOrder = "c|x|e"
        localDB.setItem("SortByTypeOrder", "c|x|e");
        $("#sort_by_type").prop("checked", true);

        UpdateBuddyList();
    });
    $("#sort_by_type_xec").change(function(){
        BuddySortBy = "type";
        localDB.setItem("BuddySortBy", "type");
        SortByTypeOrder = "x|e|c"
        localDB.setItem("SortByTypeOrder", "x|e|c");
        $("#sort_by_type").prop("checked", true);

        UpdateBuddyList();
    });
    $("#sort_by_type_xce").change(function(){
        BuddySortBy = "type";
        localDB.setItem("BuddySortBy", "type");
        SortByTypeOrder = "x|e|c"
        localDB.setItem("SortByTypeOrder", "x|c|e");
        $("#sort_by_type").prop("checked", true);

        UpdateBuddyList();
    });
    $("#sort_by_type_exc").change(function(){
        BuddySortBy = "type";
        localDB.setItem("BuddySortBy", "type");
        SortByTypeOrder = "e|x|c"
        localDB.setItem("SortByTypeOrder", "e|x|c");
        $("#sort_by_type").prop("checked", true);

        UpdateBuddyList();
    });
    $("#sort_by_type_ecx").change(function(){
        BuddySortBy = "type";
        localDB.setItem("BuddySortBy", "type");
        SortByTypeOrder = "e|c|x"
        localDB.setItem("SortByTypeOrder", "e|c|x");
        $("#sort_by_type").prop("checked", true);

        UpdateBuddyList();
    });


    $("#sort_by_exten").change(function(){
        BuddySortBy = "extension";
        localDB.setItem("BuddySortBy", "extension");
        $("#sort_by_type_cex").prop("checked", false);
        $("#sort_by_type_cxe").prop("checked", false);
        $("#sort_by_type_xec").prop("checked", false);
        $("#sort_by_type_xce").prop("checked", false);
        $("#sort_by_type_exc").prop("checked", false);
        $("#sort_by_type_ecx").prop("checked", false);

        UpdateBuddyList();
    });
    $("#sort_by_alpha").change(function(){
        BuddySortBy = "alphabetical";
        localDB.setItem("BuddySortBy", "alphabetical");
        $("#sort_by_type_cex").prop("checked", false);
        $("#sort_by_type_cxe").prop("checked", false);
        $("#sort_by_type_xec").prop("checked", false);
        $("#sort_by_type_xce").prop("checked", false);
        $("#sort_by_type_exc").prop("checked", false);
        $("#sort_by_type_ecx").prop("checked", false);
        UpdateBuddyList();
    });
    $("#sort_by_activity").change(function(){
        BuddySortBy = "activity";
        localDB.setItem("BuddySortBy", "activity");
        $("#sort_by_type_cex").prop("checked", false);
        $("#sort_by_type_cxe").prop("checked", false);
        $("#sort_by_type_xec").prop("checked", false);
        $("#sort_by_type_xce").prop("checked", false);
        $("#sort_by_type_exc").prop("checked", false);
        $("#sort_by_type_ecx").prop("checked", false);

        UpdateBuddyList();
    });

    $("#sort_auto_delete_at_end").change(function(){
        BuddyAutoDeleteAtEnd = this.checked;
        localDB.setItem("BuddyAutoDeleteAtEnd", (this.checked)? "1" : "0");

        if(this.checked){
            $("#sort_auto_delete_hide").prop("checked", false);
            HideAutoDeleteBuddies = false;
            localDB.setItem("HideAutoDeleteBuddies", "0");
        }

        UpdateBuddyList();
    });
    $("#sort_auto_delete_hide").change(function(){
        HideAutoDeleteBuddies = this.checked;
        localDB.setItem("HideAutoDeleteBuddies", (this.checked)? "1" : "0");

        if(this.checked){
            $("#sort_auto_delete_at_end").prop("checked", false);
            BuddyAutoDeleteAtEnd = false;
            localDB.setItem("BuddyAutoDeleteAtEnd", "0");
        }

        UpdateBuddyList();
    });
    $("#sort_show_exten_num").change(function(){
        BuddyShowExtenNum = this.checked;
        localDB.setItem("BuddyShowExtenNum", (this.checked)? "1" : "0");

        UpdateBuddyList();
    });

    $("#actionArea").show();
}


/**
 * Primary method for making a call.
 * @param {string} type (required) Either "audio" or "video". Will setup UI according to this type.
 * @param {Buddy} buddy (optional) The buddy to dial if provided.
 * @param {sting} numToDial (required) The number to dial.
 * @param {string} CallerID (optional) If no buddy provided, one is generated automatically using this callerID and the numToDial
 * @param {Array<string>} extraHeaders = (optional) Array of headers to include in the INVITE eg: ["foo: bar"] (Note the space after the :)
 */
function DialByLine(type, NumberToBeCalled ,buddy, CallerID, extraHeaders){
    if(userAgent == null || userAgent.isRegistered() == false){
        ShowMyProfile();
        return;
    }

    var numDial = NumberToBeCalled;
    if(EnableAlphanumericDial){
        numDial = numDial.replace(telAlphanumericRegEx, "").substring(0,MaxDidLength);
    }
    else {
        numDial = numDial.replace(telNumericRegEx, "").substring(0,MaxDidLength);
    }
    if(numDial.length == 0) {
        console.warn("Enter number to dial");
        return;
    }


    // Create a Buddy if one is not already existing
    var buddyObj = (buddy)? FindBuddyByIdentity(buddy) : FindBuddyByDid(numDial);
    if(buddyObj == null) {
        var buddyType = (numDial.length > DidLength)? "contact" : "extension";
        // Assumption but anyway: If the number starts with a * or # then its probably not a subscribable did,
        // and is probably a feature code.
        if(numDial.substring(0,1) == "*" || numDial.substring(0,1) == "#") buddyType = "contact";
        buddyObj = MakeBuddy(buddyType, true, false, false, (CallerID)? CallerID : numDial, numDial, null, false, null, AutoDeleteDefault);
    }

    // Create a Line
    newLineNumber = newLineNumber + 1;
    var lineObj = new Line(newLineNumber, buddyObj.CallerIDName, numDial, buddyObj);
    Lines.push(lineObj);
    AddLineHtml(lineObj, "outbound");
    SelectLine(newLineNumber);

    // Start Call Invite
    if(type == "audio"){
        AudioCall(lineObj, numDial, extraHeaders);
    }


    try{
        $("#line-" + newLineNumber).get(0).scrollIntoViewIfNeeded();
    } catch(e){}
}


function SelectLine(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null) return;

    var displayLineNumber = 0;
    for(var l = 0; l < Lines.length; l++) {
        if(Lines[l].LineNumber == lineObj.LineNumber) displayLineNumber = l+1;
        if(Lines[l].IsSelected == true && Lines[l].LineNumber == lineObj.LineNumber){
            // Nothing to do, you re-selected the same buddy;
            return;
        }
    }

    console.log("Selecting Line : "+ lineObj.LineNumber);

    // Can only display one thing on the Right
    $(".streamSelected").each(function () {
        $(this).prop('class', 'stream');
    });
    $("#line-ui-" + lineObj.LineNumber).prop('class', 'streamSelected');

    $("#line-ui-" + lineObj.LineNumber + "-DisplayLineNo").html("<i class=\"fa fa-phone\"></i> "+ lang.line +" "+ displayLineNumber);
    $("#line-ui-" + lineObj.LineNumber + "-LineIcon").html(displayLineNumber);

    // Switch the SIP Sessions
    SwitchLines(lineObj.LineNumber);

    // Update Lines List
    for(var l = 0; l < Lines.length; l++) {
        var classStr = (Lines[l].LineNumber == lineObj.LineNumber)? "buddySelected" : "buddy";
        if(Lines[l].SipSession != null) classStr = (Lines[l].SipSession.isOnHold)? "buddyActiveCallHollding" : "buddyActiveCall";

        $("#line-" + Lines[l].LineNumber).prop('class', classStr);
        Lines[l].IsSelected = (Lines[l].LineNumber == lineObj.LineNumber);
    }
    // Update Buddy List
    for(var b = 0; b < Buddies.length; b++) {
        $("#contact-" + Buddies[b].identity).prop("class", "buddy");
        Buddies[b].IsSelected = false;
    }

    // Change to Stream if in Narrow view
    UpdateUI();
}
function FindLineByNumber(lineNum) {
    for(var l = 0; l < Lines.length; l++) {
        if(Lines[l].LineNumber == lineNum) return Lines[l];
    }
    return null;
}

function AddLineHtml(lineObj){


    var html = "<table id=\"line-ui-"+ lineObj.LineNumber +"\" class=stream cellspacing=0 cellpadding=0>";
    html += "<tr><td class=\"highlightSection\">";
    html += "<div style=\"display:none;\">";
    html += "<audio id=\"line-"+ lineObj.LineNumber +"-remoteAudio\"></audio>";
    html += "</div>";
    html += "</td></tr>";
    html += "<tr><td id=\"line-"+ lineObj.LineNumber +"-call-fullscreen\" >"
    html += "<div id=\"line-"+ lineObj.LineNumber +"-ActiveCall\" style=\"display:none; position: absolute; bottom: 0px; right: 0px;\">";
    html += "<div class=\"z-index: 99;text-align: center;bottom: 0px;width: 100%;left: 0px;\">";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-End\" onclick=\"endSession('"+ lineObj.LineNumber +"')\" class=\"roundButtons dialButtons inCallButtons hangupButton\" title=\""+ lang.end_call +"\"><i class=\"fa fa-phone\" style=\"transform: rotate(225deg);font-size: 30px;\"></i><div id=\"line-"+ lineObj.LineNumber +"-timer\" style=\"font-size: 10px;margin-top: -5px;width: 100%;\">00:00</div></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-Mute\" onclick=\"MuteSession('"+ lineObj.LineNumber +"')\" style=\"margin-right: -5px;background-color: rgb(0, 63, 145);border-radius: 0px;border-left: 1px solid rgb(1, 49, 110);\" class=\"roundButtons dialButtons inCallButtons\" title=\""+ lang.mute +"\"><i class=\"fa fa-microphone-slash\"></i></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-Unmute\" onclick=\"UnmuteSession('"+ lineObj.LineNumber +"')\" class=\"roundButtons dialButtons inCallButtons\" title=\""+ lang.unmute +"\" style=\"margin-right: -5px;background-color: rgb(0, 63, 145);border-radius: 0px;border-left: 1px solid rgb(1, 49, 110);display:none\"><i class=\"fa fa-microphone\"></i></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-Hold\" onclick=\"holdSession('"+ lineObj.LineNumber +"')\" class=\"roundButtons dialButtons inCallButtons\" style=\"background-color: rgb(0, 63, 145);border-radius: 0px;border-left: 1px solid rgb(1, 49, 110);margin-right: -1px;\"  title=\""+ lang.hold_call +"\"><i class=\"fa fa-pause-circle\"></i></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-Unhold\" onclick=\"unholdSession('"+ lineObj.LineNumber +"')\" class=\"roundButtons dialButtons inCallButtons\" style=\"color: red;margin-right: -1px;border-radius: 0px;background-color: rgb(0, 63, 145);border-left: 1px solid rgb(1, 49, 110);\" title=\""+ lang.resume_call +"\" style=\"color: red; display:none\"><i class=\"fa fa-play-circle\"></i></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-settings\" onclick=\"ChangeSettings('"+ lineObj.LineNumber +"', this)\" class=\"roundButtons dialButtons inCallButtons\" style=\"border-radius: 0 30px 30px 0;background: #003f91;border-left: 1px solid #01316e;margin: 0;\" title=\""+ lang.device_settings +"\"><i class=\"fa fa-volume-up\"></i></button>";
    html += "</div>";
    html += "</div>";
    html += "</div>"; // Active Call UI

    html += "<div id=\"line-"+ lineObj.LineNumber +"-progress\"  style=\"display:none; position: absolute; bottom: 0px; right: 0px;display:none\">";
    html += "<div class=\"z-index: 99;text-align: center;bottom: 0px;width: 100%;left: 0px;\">";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-End\" onclick=\"cancelSession('"+ lineObj.LineNumber +"')\" class=\"roundButtons dialButtons inCallButtons hangupButton\" title=\""+ lang.end_call +"\"><i class=\"fa fa-phone\" style=\"transform: rotate(225deg);font-size: 30px;\"></i><div id=\"line-"+ lineObj.LineNumber +"-timer\" style=\"font-size: 10px;margin-top: -5px;width: 100%;\">00:00</div></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-Mute\" style=\"margin-right: -5px;background-color: rgb(0, 63, 145);border-radius: 0px;border-left: 1px solid rgb(1, 49, 110);\" class=\"roundButtons dialButtons inCallButtons\" title=\""+ lang.mute +"\"><i class=\"fa fa-microphone-slash\"></i></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-Unmute\" class=\"roundButtons dialButtons inCallButtons\" title=\""+ lang.unmute +"\" style=\"margin-right: -5px;background-color: rgb(0, 63, 145);border-radius: 0px;border-left: 1px solid rgb(1, 49, 110);display:none\"><i class=\"fa fa-microphone\"></i></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-Hold\" class=\"roundButtons dialButtons inCallButtons\" style=\"background-color: rgb(0, 63, 145);border-radius: 0px;border-left: 1px solid rgb(1, 49, 110);margin-right: -1px;\"  title=\""+ lang.hold_call +"\"><i class=\"fa fa-pause-circle\"></i></button>";
    html += "<button id=\"line-"+ lineObj.LineNumber +"-btn-settings\" class=\"roundButtons dialButtons inCallButtons\" style=\"border-radius: 0 30px 30px 0;background: #003f91;border-left: 1px solid #01316e;margin: 0;\" title=\""+ lang.device_settings +"\"><i class=\"fa fa-volume-up\"></i></button>";
    html += "</div>";
    html += "</div>";

    html += "</td></tr>";
    html += "</table>";

    $("#rightContent").append(html);

    $("#line-"+ lineObj.LineNumber +"-AudioOrVideoCall").on("click", function(){
        RestoreCallControls(lineObj.LineNumber);
    });
}

function RemoveLine(lineObj){
    if(lineObj == null) return;

    var earlyReject = lineObj.SipSession.data.earlyReject;
    for(var l = 0; l < Lines.length; l++) {
        if(Lines[l].LineNumber == lineObj.LineNumber) {
            Lines.splice(l,1);
            break;
        }
    }

    if(earlyReject != true){
        CloseLine(lineObj.LineNumber);
        $("#line-ui-"+ lineObj.LineNumber).remove();
    }

    UpdateBuddyList();

    if(earlyReject != true){
        // Rather than showing nothing, go to the last Buddy Selected
        // Select Last user
        if(localDB.getItem("SelectedBuddy") != null){
            console.log("Selecting previously selected buddy...", localDB.getItem("SelectedBuddy"));
            SelectBuddy(localDB.getItem("SelectedBuddy"));
            UpdateUI();
        }
    }
}
function CloseLine(lineNum){
    // Lines and Buddies (Left)
    $(".buddySelected").each(function () {
        $(this).prop('class', 'buddy');
    });
    // Streams (Right)
    $(".streamSelected").each(function () {
        $(this).prop('class', 'stream');
    });

    // SwitchLines(0);

    console.log("Closing Line: "+ lineNum);
    for(var l = 0; l < Lines.length; l++){
        Lines[l].IsSelected = false;
    }
    selectedLine = null;
    for(var b = 0; b < Buddies.length; b++){
        Buddies[b].IsSelected = false;
    }
    selectedBuddy = null;

    // Save Selected
    // localDB.setItem("SelectedBuddy", null);

    // Change to Stream if in Narrow view
    UpdateUI();
}
function SwitchLines(lineNum){
    $.each(userAgent.sessions, function (i, session) {
        // All the other calls, not on hold
        if(session.state == SIP.SessionState.Established){
            if(session.isOnHold == false && session.data.line != lineNum) {
                holdSession(session.data.line);
            }
        }
        session.data.IsCurrentCall = false;
    });

    var lineObj = FindLineByNumber(lineNum);
    if(lineObj != null && lineObj.SipSession != null) {
        var session = lineObj.SipSession;
        if(session.state == SIP.SessionState.Established){
            if(session.isOnHold == true) {
                unholdSession(lineNum)
            }
        }
        session.data.IsCurrentCall = true;
    }
    selectedLine = lineNum;

    RefreshLineActivity(lineNum);
}
function RefreshLineActivity(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) {
        return;
    }
    var session = lineObj.SipSession;

    $("#line-"+ lineNum +"-CallDetails").empty();

    var callDetails = [];

    var ringTime = 0;
    var CallStart = moment.utc(session.data.callstart.replace(" UTC", ""));
    var CallAnswer = null;
    if(session.data.startTime){
        CallAnswer = moment.utc(session.data.startTime);
        ringTime = moment.duration(CallAnswer.diff(CallStart));
    }
    CallStart = CallStart.format("YYYY-MM-DD HH:mm:ss UTC")
    CallAnswer = (CallAnswer)? CallAnswer.format("YYYY-MM-DD HH:mm:ss UTC") : null,
    ringTime = (ringTime != 0)? ringTime.asSeconds() : 0

    var srcCallerID = "";
    var dstCallerID = "";
    if(session.data.calldirection == "inbound") {
        srcCallerID = "<"+ session.remoteIdentity.uri.user +"> "+ session.remoteIdentity.displayName;
    }
    else if(session.data.calldirection == "outbound") {
        dstCallerID = session.data.dst;
    }

    var withVideo = (session.data.withvideo)? "("+ lang.with_video +")" : "";
    var startCallMessage = (session.data.calldirection == "inbound")? lang.you_received_a_call_from + " " + srcCallerID  +" "+ withVideo : lang.you_made_a_call_to + " " + dstCallerID +" "+ withVideo;
    callDetails.push({
        Message: startCallMessage,
        TimeStr : CallStart
    });
    if(CallAnswer){
        var answerCallMessage = (session.data.calldirection == "inbound")? lang.you_answered_after + " " + ringTime + " " + lang.seconds_plural : lang.they_answered_after + " " + ringTime + " " + lang.seconds_plural;
        callDetails.push({
            Message: answerCallMessage,
            TimeStr : CallAnswer
        });
    }

    var Transfers = (session.data.transfer)? session.data.transfer : [];
    $.each(Transfers, function(item, transfer){
        var msg = (transfer.type == "Blind")? lang.you_started_a_blind_transfer_to +" "+ transfer.to +". " : lang.you_started_an_attended_transfer_to + " "+ transfer.to +". ";
        if(transfer.accept && transfer.accept.complete == true){
            msg += lang.the_call_was_completed
        }
        else if(transfer.accept.disposition != "") {
            msg += lang.the_call_was_not_completed +" ("+ transfer.accept.disposition +")"
        }
        callDetails.push({
            Message : msg,
            TimeStr : transfer.transferTime
        });
    });
    var Mutes = (session.data.mute)? session.data.mute : []
    $.each(Mutes, function(item, mute){
        callDetails.push({
            Message : (mute.event == "mute")? lang.you_put_the_call_on_mute : lang.you_took_the_call_off_mute,
            TimeStr : mute.eventTime
        });
    });
    var Holds = (session.data.hold)? session.data.hold : []
    $.each(Holds, function(item, hold){
        callDetails.push({
            Message : (hold.event == "hold")? lang.you_put_the_call_on_hold : lang.you_took_the_call_off_hold,
            TimeStr : hold.eventTime
        });
    });
    var ConfbridgeEvents = (session.data.ConfbridgeEvents)? session.data.ConfbridgeEvents : []
    $.each(ConfbridgeEvents, function(item, event){
        callDetails.push({
            Message : event.event,
            TimeStr : event.eventTime
        });
    });
    var Recordings = (session.data.recordings)? session.data.recordings : []
    $.each(Recordings, function(item, recording){
        var msg = lang.call_is_being_recorded;
        if(recording.startTime != recording.stopTime){
            msg += "("+ lang.now_stopped +")"
        }
        callDetails.push({
            Message : msg,
            TimeStr : recording.startTime
        });
    });
    var ConfCalls = (session.data.confcalls)? session.data.confcalls : []
    $.each(ConfCalls, function(item, confCall){
        var msg = lang.you_started_a_conference_call_to +" "+ confCall.to +". ";
        if(confCall.accept && confCall.accept.complete == true){
            msg += lang.the_call_was_completed
        }
        else if(confCall.accept.disposition != "") {
            msg += lang.the_call_was_not_completed +" ("+ confCall.accept.disposition +")"
        }
        callDetails.push({
            Message : msg,
            TimeStr : confCall.startTime
        });
    });

    callDetails.sort(function(a, b){
        var aMo = moment.utc(a.TimeStr.replace(" UTC", ""));
        var bMo = moment.utc(b.TimeStr.replace(" UTC", ""));
        if (aMo.isSameOrAfter(bMo, "second")) {
            return -1;
        } else return 1;
        return 0;
    });

    $.each(callDetails, function(item, detail){
        var Time = moment.utc(detail.TimeStr.replace(" UTC", "")).local().format(DisplayTimeFormat);
        var messageString = "<table class=timelineMessage cellspacing=0 cellpadding=0><tr>"
        messageString += "<td class=timelineMessageArea>"
        messageString += "<div class=timelineMessageDate><i class=\"fa fa-circle timelineMessageDot\"></i>"+ Time +"</div>"
        messageString += "<div class=timelineMessageText>"+ detail.Message +"</div>"
        messageString += "</td>"
        messageString += "</tr></table>";
        $("#line-"+ lineNum +"-CallDetails").prepend(messageString);
    });
}

// Buddy & Contacts
// ================
var Buddy = function(type, identity, CallerIDName, ExtNo, MobileNumber, ContactNumber1, ContactNumber2, lastActivity, desc, Email, jid, dnd, subscribe, subscription, autoDelete, pinned){
    this.type = type; // extension | xmpp | contact | group
    this.identity = identity;
    this.jid = jid;
    this.CallerIDName = (CallerIDName)? CallerIDName : "";
    this.Email = (Email)? Email : "" ;
    this.Desc = (desc)? desc : "" ;
    this.ExtNo = ExtNo;
    this.MobileNumber = MobileNumber;
    this.ContactNumber1 = ContactNumber1;
    this.ContactNumber2 = ContactNumber2;
    this.lastActivity = lastActivity; // Full Date as string eg "1208-03-21 15:34:23 UTC"
    this.devState = "dotOffline";
    this.presence = "Unknown";
    this.missed = 0;
    this.IsSelected = false;
    this.imageObjectURL = "";
    this.presenceText = lang.default_status;
    this.EnableDuringDnd = dnd;
    this.EnableSubscribe = subscribe;
    this.SubscribeUser = (subscription)? subscription : ExtNo;
    this.AllowAutoDelete = (typeof autoDelete !== 'undefined')? autoDelete : AutoDeleteDefault;
    this.Pinned = (typeof pinned !== 'undefined')? pinned : false;
}
function InitUserBuddies(){
    var template = { TotalRows:0, DataCollection:[] }
    localDB.setItem(profileUserID + "-Buddies", JSON.stringify(template));
    return JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
}

/**
 * Method used to create a permanent buddy (saved to the local store).
 * Note: This method also makes the memory object for display it on the left hand side, using AddBuddy()
 * @param {string} type One of extension | xmpp | contact | group
 * @param {boolean} update Option to issue UpdateBuddyList() once done.
 * @param {boolean} focus Option to focus/select the buddy once done.
 * @param {boolean} subscribe Option to create a subscription to the user. (also see subscribeUser)
 * @param {string} callerID The Display Name or Caller ID of the Buddy
 * @param {string} did The Extension Number/DID/SipID of the Buddy
 * @param {string} jid The Jabber Identifier of the XMPP buddy (only if type=xmpp)
 * @param {boolean} AllowDuringDnd Option to allowing inbound calls when on DND
 * @param {string} subscribeUser If subscribe=true, you can optionally specify a SipID to subscribe to.
 * @param {boolean} autoDelete Option to have this buddy delete after MaxBuddyAge
**/
function MakeBuddy(type, update, focus, subscribe, callerID, did, jid, AllowDuringDnd, subscribeUser, autoDelete){
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    if(json == null) json = InitUserBuddies();

    var dateNow = utcDateNow();
    var buddyObj = null;
    var id = uID();

    if(type == "extension") {
        json.DataCollection.push({
            Type: "extension",
            LastActivity: dateNow,
            ExtensionNumber: did,
            MobileNumber: "",
            ContactNumber1: "",
            ContactNumber2: "",
            uID: id,
            cID: null,
            gID: null,
            jid: null,
            DisplayName: callerID,
            Description: "",
            Email: "",
            MemberCount: 0,
            EnableDuringDnd: AllowDuringDnd,
            Subscribe: subscribe,
            SubscribeUser: subscribeUser,
            AutoDelete: autoDelete
        });
        buddyObj = new Buddy("extension", id, callerID, did, "", "", "", dateNow, "", "", null, AllowDuringDnd, subscribe, subscribeUser, autoDelete);
        AddBuddy(buddyObj, update, focus, subscribe, true);
    }
    if(type == "xmpp") {
        json.DataCollection.push({
            Type: "xmpp",
            LastActivity: dateNow,
            ExtensionNumber: did,
            MobileNumber: "",
            ContactNumber1: "",
            ContactNumber2: "",
            uID: id,
            cID: null,
            gID: null,
            jid: jid,
            DisplayName: callerID,
            Description: "",
            Email: "",
            MemberCount: 0,
            EnableDuringDnd: AllowDuringDnd,
            Subscribe: subscribe,
            SubscribeUser: subscribeUser,
            AutoDelete: autoDelete
        });
        buddyObj = new Buddy("xmpp", id, callerID, did, "", "", "", dateNow, "", "", jid, AllowDuringDnd, subscribe, subscribeUser, autoDelete);
        AddBuddy(buddyObj, update, focus, subscribe, true);
    }
    if(type == "contact"){
        json.DataCollection.push({
            Type: "contact",
            LastActivity: dateNow,
            ExtensionNumber: "",
            MobileNumber: "",
            ContactNumber1: did,
            ContactNumber2: "",
            uID: null,
            cID: id,
            gID: null,
            jid: null,
            DisplayName: callerID,
            Description: "",
            Email: "",
            MemberCount: 0,
            EnableDuringDnd: AllowDuringDnd,
            Subscribe: false,
            SubscribeUser: null,
            AutoDelete: autoDelete
        });
        buddyObj = new Buddy("contact", id, callerID, "", "", did, "", dateNow, "", "", null, AllowDuringDnd, false, null, autoDelete);
        AddBuddy(buddyObj, update, focus, false, true);
    }
    if(type == "group") {
        json.DataCollection.push({
            Type: "group",
            LastActivity: dateNow,
            ExtensionNumber: did,
            MobileNumber: "",
            ContactNumber1: "",
            ContactNumber2: "",
            uID: null,
            cID: null,
            gID: id,
            jid: null,
            DisplayName: callerID,
            Description: "",
            Email: "",
            MemberCount: 0,
            EnableDuringDnd: false,
            Subscribe: false,
            SubscribeUser: null,
            AutoDelete: autoDelete
        });
        buddyObj = new Buddy("group", id, callerID, did, "", "", "", dateNow, "", "", null, false, false, null, autoDelete);
        AddBuddy(buddyObj, update, focus, false, true);
    }
    // Update Size:
    json.TotalRows = json.DataCollection.length;

    // Save To DB
    localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));

    // Return new buddy
    return buddyObj;
}
function UpdateBuddyCallerID(buddyObj, callerID){
    buddyObj.CallerIDName = callerID;

    var buddy = buddyObj.identity;
    // Update DB
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    if(json != null){
        $.each(json.DataCollection, function (i, item) {
            if(item.uID == buddy || item.cID == buddy || item.gID == buddy){
                item.DisplayName = callerID;
                return false;
            }
        });
        // Save To DB
        localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));
    }

    UpdateBuddyList();
}
function AddBuddy(buddyObj, update, focus, subscribe, cleanup){
    Buddies.push(buddyObj);
    if(update == true) UpdateBuddyList();
    if(subscribe == true) SubscribeBuddy(buddyObj);
    if(focus == true) SelectBuddy(buddyObj.identity);
    if(cleanup == true) CleanupBuddies()
}
function CleanupBuddies(){
    if(MaxBuddyAge > 1 || MaxBuddies > 1){
        // Sort According to .lastActivity
        Buddies.sort(function(a, b){
            var aMo = moment.utc(a.lastActivity.replace(" UTC", ""));
            var bMo = moment.utc(b.lastActivity.replace(" UTC", ""));
            if (aMo.isSameOrAfter(bMo, "second")) {
                return -1;
            } else return 1;
            return 0;
        });

        if(MaxBuddyAge > 1){
            var expiredDate = moment.utc().subtract(MaxBuddyAge, 'days');
            console.log("Running Buddy Cleanup for activity older than: ", expiredDate.format(DisplayDateFormat+" "+DisplayTimeFormat));
            for (var b = Buddies.length - 1; b >= 0; b--) {
                var lastActivity = moment.utc(Buddies[b].lastActivity.replace(" UTC", ""));
                if(lastActivity.isSameOrAfter(expiredDate, "second")){
                    // This one is fine
                } else {
                    // Too Old
                    if(Buddies[b].AllowAutoDelete == true){
                        console.warn("This buddy is too old, and will be deleted: ", lastActivity.format(DisplayDateFormat+" "+DisplayTimeFormat));
                        DoRemoveBuddy(Buddies[b].identity);
                    }
                }
            }
        }
        if(MaxBuddies > 1 && MaxBuddies < Buddies.length){
            console.log("Running Buddy Cleanup for buddies more than: ", MaxBuddies);
            for (var b = Buddies.length - 1; b >= MaxBuddies; b--) {
                if(Buddies[b].AllowAutoDelete == true){
                    console.warn("This buddy is too Many, and will be deleted: ", Buddies[b].identity);
                    DoRemoveBuddy(Buddies[b].identity);
                }
            }
        }
    }
}
function PopulateBuddyList() {
    console.log("Clearing Buddies...");
    Buddies = new Array();
    console.log("Adding Buddies...");
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    if(json == null) json = InitUserBuddies();

    console.log("Total Buddies: " + json.TotalRows);
    $.each(json.DataCollection, function (i, item) {
        item.AutoDelete = (item.AutoDelete == true)? true : false;
        item.Pinned = (item.Pinned == true)? true : false;
        if(item.Type == "extension"){
            // extension
            var buddy = new Buddy("extension",
                                    item.uID,
                                    item.DisplayName,
                                    item.ExtensionNumber,
                                    item.MobileNumber,
                                    item.ContactNumber1,
                                    item.ContactNumber2,
                                    item.LastActivity,
                                    item.Description,
                                    item.Email,
                                    null,
                                    item.EnableDuringDnd,
                                    item.Subscribe,
                                    item.SubscribeUser,
                                    item.AutoDelete,
                                    item.Pinned);
            AddBuddy(buddy, false, false, false);
        }
        else if(item.Type == "xmpp"){
            // xmpp
            var buddy = new Buddy("xmpp",
                                    item.uID,
                                    item.DisplayName,
                                    item.ExtensionNumber,
                                    "",
                                    "",
                                    "",
                                    item.LastActivity,
                                    "",
                                    "",
                                    item.jid,
                                    item.EnableDuringDnd,
                                    item.Subscribe,
                                    item.SubscribeUser,
                                    item.AutoDelete,
                                    item.Pinned);
            AddBuddy(buddy, false, false, false);
        }
        else if(item.Type == "contact"){
            // contact
            var buddy = new Buddy("contact",
                                    item.cID,
                                    item.DisplayName,
                                    "",
                                    item.MobileNumber,
                                    item.ContactNumber1,
                                    item.ContactNumber2,
                                    item.LastActivity,
                                    item.Description,
                                    item.Email,
                                    null,
                                    item.EnableDuringDnd,
                                    item.Subscribe,
                                    item.SubscribeUser,
                                    item.AutoDelete,
                                    item.Pinned);
            AddBuddy(buddy, false, false, false);
        }
        else if(item.Type == "group"){
            // group
            var buddy = new Buddy("group",
                                    item.gID,
                                    item.DisplayName,
                                    item.ExtensionNumber,
                                    "",
                                    "",
                                    "",
                                    item.LastActivity,
                                    item.MemberCount + " member(s)",
                                    item.Email,
                                    null,
                                    item.EnableDuringDnd,
                                    item.Subscribe,
                                    item.SubscribeUser,
                                    item.AutoDelete,
                                    item.Pinned);
            AddBuddy(buddy, false, false, false);
        }
    });
    CleanupBuddies()

    // Update List (after add)
    console.log("Updating Buddy List...");
    UpdateBuddyList();
}
function UpdateBuddyList(){
    var filter = $("#txtFindBuddy").val();

    $("#myContacts").empty();

    // Show Lines
    var callCount = 0
    for(var l = 0; l < Lines.length; l++) {

        var classStr = (Lines[l].IsSelected)? "buddySelected" : "buddy";
        if(Lines[l].SipSession != null) classStr = (Lines[l].SipSession.isOnHold)? "buddyActiveCallHollding" : "buddyActiveCall";

        var html = "<div id=\"line-"+ Lines[l].LineNumber +"\" class="+ classStr +" onclick=\"SelectLine('"+ Lines[l].LineNumber +"')\">";
        if(Lines[l].IsSelected == false && Lines[l].SipSession && Lines[l].SipSession.data.started != true && Lines[l].SipSession.data.calldirection == "inbound"){
            html += "<span id=\"line-"+ Lines[l].LineNumber +"-ringing\" class=missedNotifyer style=\"padding-left: 5px; padding-right: 5px; width:unset\"><i class=\"fa fa-phone\"></i> "+ lang.state_ringing +"</span>";
        }
        html += "<div class=lineIcon>"+ (l + 1) +"</div>";
        html += "<div class=contactNameText><i class=\"fa fa-phone\"></i> "+ lang.line +" "+ (l + 1) +"</div>";
        html += "<div id=\"line-"+ Lines[l].LineNumber +"-datetime\" class=contactDate>&nbsp;</div>";
        html += "<div class=presenceText>"+ Lines[l].DisplayName +" <"+ Lines[l].DisplayNumber +">" +"</div>";
        html += "</div>";
        // SIP.Session.C.STATUS_TERMINATED
        if(Lines[l].SipSession && Lines[l].SipSession.data.earlyReject != true){
            $("#myContacts").append(html);
            callCount ++;
        }
    }

    // Draw a line if there are calls
    if(callCount > 0){
        $("#myContacts").append("<hr class=hrline>");
    }



    // Sort and filter
    SortBuddies();

    var hiddenBuddies = 0;

    // Display
    for(var b = 0; b < Buddies.length; b++) {
        var buddyObj = Buddies[b];

        if(filter && filter.length >= 1){
            // Perform Filter Display
            var display = false;
            if(buddyObj.CallerIDName && buddyObj.CallerIDName.toLowerCase().indexOf(filter.toLowerCase()) > -1 ) display = true;
            if(buddyObj.ExtNo && buddyObj.ExtNo.toLowerCase().indexOf(filter.toLowerCase()) > -1 ) display = true;
            if(buddyObj.Desc && buddyObj.Desc.toLowerCase().indexOf(filter.toLowerCase()) > -1 ) display = true;
            if(!display) continue;
        }

        var today = moment.utc();
        var lastActivity = moment.utc(buddyObj.lastActivity.replace(" UTC", ""));
        var displayDateTime = "";
        if(lastActivity.isSame(today, 'day'))
        {
            displayDateTime = lastActivity.local().format(DisplayTimeFormat);
        }
        else {
            displayDateTime = lastActivity.local().format(DisplayDateFormat);
        }

        if(HideAutoDeleteBuddies){
            if(buddyObj.AllowAutoDelete) {
                hiddenBuddies++;
                continue;
            }
        }

        var classStr = (buddyObj.IsSelected)? "buddySelected" : "buddy";
        if(buddyObj.type == "extension") {
            var friendlyState = buddyObj.presence;
            if(friendlyState == "Unknown") friendlyState = lang.state_unknown;
            if(friendlyState == "Not online") friendlyState = lang.state_not_online;
            if(friendlyState == "Ready") friendlyState = lang.state_ready;
            if(friendlyState == "On the phone") friendlyState = lang.state_on_the_phone;
            if(friendlyState == "Proceeding") friendlyState = lang.state_on_the_phone;
            if(friendlyState == "Ringing") friendlyState = lang.state_ringing;
            if(friendlyState == "On hold") friendlyState = lang.state_on_hold;
            if(friendlyState == "Unavailable") friendlyState = lang.state_unavailable;
            if(buddyObj.EnableSubscribe != true) friendlyState = (buddyObj.Desc)? buddyObj.Desc : "";
            var autDeleteStatus = "";
            if(buddyObj.AllowAutoDelete == true) autDeleteStatus = "<i class=\"fa fa-clock-o\"></i> ";
            var html = "<div id=\"contact-"+ buddyObj.identity +"\" class="+ classStr +" onclick=\"SelectBuddy('"+ buddyObj.identity +"', 'extension')\">";
            html += "<span id=\"contact-"+ buddyObj.identity +"-missed\" class=missedNotifyer style=\""+ ((buddyObj.missed && buddyObj.missed > 0)? "" : "display:none") +"\">"+ buddyObj.missed +"</span>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-picture\" class=buddyIcon style=\"background-image: url('"+ getPicture(buddyObj.identity, buddyObj.type) +"')\"></div>";
            html += (buddyObj.Pinned)? "<span class=pinnedBuddy><i class=\"fa fa-thumb-tack\"></i></span>" : "";
            html += "<div class=contactNameText>";
            html += "<span id=\"contact-"+ buddyObj.identity +"-devstate\" class=\""+ ((buddyObj.EnableSubscribe)? buddyObj.devState : "dotDefault") +"\"></span>";
            html += (BuddyShowExtenNum == true)? " "+ buddyObj.ExtNo + " - " : " ";
            html += buddyObj.CallerIDName
            html += "</div>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-datetime\" class=contactDate>"+ autDeleteStatus + ""+ displayDateTime +"</div>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-presence\" class=presenceText>"+ friendlyState +"</div>";
            html += "</div>";
            $("#myContacts").append(html);
        } else if(buddyObj.type == "xmpp") {
            var friendlyState = buddyObj.presenceText;
            var autDeleteStatus = "";
            if(buddyObj.AllowAutoDelete == true) autDeleteStatus = "<i class=\"fa fa-clock-o\"></i> ";
            // NOTE: Set by user could contain malicious code
            friendlyState = friendlyState.replace(/[<>"'\r\n&]/g, function(chr){
                let table = { '<': 'lt', '>': 'gt', '"': 'quot', '\'': 'apos', '&': 'amp', '\r': '#10', '\n': '#13' };
                return '&' + table[chr] + ';';
            });

            var html = "<div id=\"contact-"+ buddyObj.identity +"\" class="+ classStr +" onclick=\"SelectBuddy('"+ buddyObj.identity +"', 'extension')\">";
            html += "<span id=\"contact-"+ buddyObj.identity +"-missed\" class=missedNotifyer style=\""+ ((buddyObj.missed && buddyObj.missed > 0)? "" : "display:none") +"\">"+ buddyObj.missed +"</span>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-picture\" class=buddyIcon style=\"background-image: url('"+ getPicture(buddyObj.identity, buddyObj.type) +"')\"></div>";
            html += (buddyObj.Pinned)? "<span class=pinnedBuddy><i class=\"fa fa-thumb-tack\"></i></span>" : "";
            html += "<div class=contactNameText>";
            html += "<span id=\"contact-"+ buddyObj.identity +"-devstate\" class=\""+ ((buddyObj.EnableSubscribe)? buddyObj.devState : "dotDefault") +"\"></span>";
            html += (BuddyShowExtenNum == true)? " "+ buddyObj.ExtNo + " - " : " ";
            html += buddyObj.CallerIDName;
            html += "</div>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-datetime\" class=contactDate>"+ autDeleteStatus + ""+ displayDateTime +"</div>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-presence\" class=presenceText><i class=\"fa fa-comments\"></i> "+ friendlyState +"</div>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-chatstate-menu\" class=presenceText style=\"display:none\"><i class=\"fa fa-commenting-o\"></i> "+ buddyObj.CallerIDName +" "+ lang.is_typing +"...</div>";
            html += "</div>";
            $("#myContacts").append(html);
        } else if(buddyObj.type == "contact") {
            var autDeleteStatus = "";
            if(buddyObj.AllowAutoDelete == true) autDeleteStatus = "<i class=\"fa fa-clock-o\"></i> ";
            var html = "<div id=\"contact-"+ buddyObj.identity +"\" class="+ classStr +" onclick=\"SelectBuddy('"+ buddyObj.identity +"', 'contact')\">";
            html += "<span id=\"contact-"+ buddyObj.identity +"-missed\" class=missedNotifyer style=\""+ ((buddyObj.missed && buddyObj.missed > 0)? "" : "display:none") +"\">"+ buddyObj.missed +"</span>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-picture\" class=buddyIcon style=\"background-image: url('"+ getPicture(buddyObj.identity, buddyObj.type) +"')\"></div>";
            html += (buddyObj.Pinned)? "<span class=pinnedBuddy><i class=\"fa fa-thumb-tack\"></i></span>" : "";
            html += "<div class=contactNameText><i class=\"fa fa-address-card\"></i> "+ buddyObj.CallerIDName +"</div>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-datetime\" class=contactDate>"+ autDeleteStatus + ""+ displayDateTime +"</div>";
            html += "<div class=presenceText>"+ buddyObj.Desc +"</div>";
            html += "</div>";
            $("#myContacts").append(html);
        } else if(buddyObj.type == "group"){
            var autDeleteStatus = "";
            if(buddyObj.AllowAutoDelete == true) autDeleteStatus = "<i class=\"fa fa-clock-o\"></i> ";
            var html = "<div id=\"contact-"+ buddyObj.identity +"\" class="+ classStr +" onclick=\"SelectBuddy('"+ buddyObj.identity +"', 'group')\">";
            html += "<span id=\"contact-"+ buddyObj.identity +"-missed\" class=missedNotifyer style=\""+ ((buddyObj.missed && buddyObj.missed > 0)? "" : "display:none") +"\">"+ buddyObj.missed +"</span>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-picture\" class=buddyIcon style=\"background-image: url('"+ getPicture(buddyObj.identity, buddyObj.type) +"')\"></div>";
            html += (buddyObj.Pinned)? "<span class=pinnedBuddy><i class=\"fa fa-thumb-tack\"></i></span>" : "";
            html += "<div class=contactNameText><i class=\"fa fa-users\"></i> "+ buddyObj.CallerIDName +"</div>";
            html += "<div id=\"contact-"+ buddyObj.identity +"-datetime\" class=contactDate>"+ autDeleteStatus + ""+ displayDateTime +"</div>";
            html += "<div class=presenceText>"+ buddyObj.Desc +"</div>";
            html += "</div>";
            $("#myContacts").append(html);
        }
    }
    if(hiddenBuddies > 0){
        console.warn("Auto Delete Buddies not shown", hiddenBuddies);
        var html = "<div id=hiddenBuddies class=hiddenBuddiesText>("+ lang.sort_no_showing.replace("{0}", hiddenBuddies) +")</div>";
        $("#myContacts").append(html);
        $("#hiddenBuddies").on("click", function(){
            HideAutoDeleteBuddies = false;
            // Show now, but leave default set in storage
            UpdateBuddyList();
        });
    }


    // Make Select
    // ===========
    for(var b = 0; b < Buddies.length; b++) {
        if(Buddies[b].IsSelected) {
            SelectBuddy(Buddies[b].identity, Buddies[b].type);
            break;
        }
    }
}

function RemoveBuddyMessageStream(buddyObj, days){
    // use days to specify how many days back must the records be cleared
    // eg: 30, will only remove records older than 30 day from now
    // and leave the buddy in place.
    // Must be greater then 0 or the entire buddy will be removed.
    if(buddyObj == null) return;

    // Grab a copy of the stream
    var stream = JSON.parse(localDB.getItem(buddyObj.identity + "-stream"));
    if(days && days > 0){
        if(stream && stream.DataCollection && stream.DataCollection.length >= 1){

            // Create Trim Stream
            var trimmedStream = {
                TotalRows : 0,
                DataCollection : []
            }
            trimmedStream.DataCollection = stream.DataCollection.filter(function(item){
                // Apply Date Filter
                var itemDate = moment.utc(item.ItemDate.replace(" UTC", ""));
                var expiredDate = moment().utc().subtract(days, 'days');
                // Condition
                if(itemDate.isSameOrAfter(expiredDate, "second")){
                    return true // return true to include;
                }
                else {
                    return false; // return false to exclude;
                }
            });
            trimmedStream.TotalRows = trimmedStream.DataCollection.length;
            localDB.setItem(buddyObj.identity + "-stream", JSON.stringify(trimmedStream));

            // Create Delete Stream
            var deleteStream = {
                TotalRows : 0,
                DataCollection : []
            }
            deleteStream.DataCollection = stream.DataCollection.filter(function(item){
                // Apply Date Filter
                var itemDate = moment.utc(item.ItemDate.replace(" UTC", ""));
                var expiredDate = moment().utc().subtract(days, 'days');
                // Condition
                if(itemDate.isSameOrAfter(expiredDate, "second")){
                    return false; // return false to exclude;
                }
                else {
                    return true // return true to include;
                }
            });
            deleteStream.TotalRows = deleteStream.DataCollection.length;

            // Re-assign stream so that the normal delete action can apply
            stream = deleteStream;

            RefreshStream(buddyObj);
        }
    }
    else {
        CloseBuddy(buddyObj.identity);

        // Remove From UI
        $("#stream-"+ buddyObj.identity).remove();

        // Remove Stream (CDRs & Messages etc)
        localDB.removeItem(buddyObj.identity + "-stream");

        // Remove Buddy
        var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
        var x = 0;
        $.each(json.DataCollection, function (i, item) {
            if(item.uID == buddyObj.identity || item.cID == buddyObj.identity || item.gID == buddyObj.identity){
                x = i;
                return false;
            }
        });
        json.DataCollection.splice(x,1);
        json.TotalRows = json.DataCollection.length;
        localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));

        // Remove Images
        localDB.removeItem("img-"+ buddyObj.identity +"-extension");
        localDB.removeItem("img-"+ buddyObj.identity +"-contact");
        localDB.removeItem("img-"+ buddyObj.identity +"-group");
    }
    UpdateBuddyList();

    // Remove Call Recordings
    if(stream && stream.DataCollection && stream.DataCollection.length >= 1){
        DeleteCallRecordings(buddyObj.identity, stream);
    }

    // Remove QOS Data
    if(stream && stream.DataCollection && stream.DataCollection.length >= 1){
        DeleteQosData(buddyObj.identity, stream);
    }
}
function DeleteCallRecordings(buddy, stream){
    var indexedDB = window.indexedDB;
    var request = indexedDB.open("CallRecordings", 1);
    request.onerror = function(event) {
        console.error("IndexDB Request Error:", event);
    }
    request.onupgradeneeded = function(event) {
        console.warn("Upgrade Required for IndexDB... probably because of first time use.");
        // If this is the case, there will be no call recordings
    }
    request.onsuccess = function(event) {
        console.log("IndexDB connected to CallRecordings");

        var IDB = event.target.result;
        if(IDB.objectStoreNames.contains("Recordings") == false){
            console.warn("IndexDB CallRecordings.Recordings does not exists");
            return;
        }
        IDB.onerror = function(event) {
            console.error("IndexDB Error:", event);
        }

        // Loop and Delete
        // Note: This database can only delete based on Primary Key
        // The Primary Key is arbitrary, but is saved in item.Recordings.uID
        $.each(stream.DataCollection, function (i, item) {
            if (item.ItemType == "CDR" && item.Recordings && item.Recordings.length) {
                $.each(item.Recordings, function (i, recording) {
                    console.log("Deleting Call Recording: ", recording.uID);
                    var objectStore = IDB.transaction(["Recordings"], "readwrite").objectStore("Recordings");
                    try{
                        var deleteRequest = objectStore.delete(recording.uID);
                        deleteRequest.onsuccess = function(event) {
                            console.log("Call Recording Deleted: ", recording.uID);
                        }
                    } catch(e){
                        console.log("Call Recording Delete failed: ", e);
                    }
                });
            }
        });
    }
}
function ToggleExtraButtons(lineNum, normal, expanded){
    var extraButtons = $("#contact-"+ lineNum +"-extra-buttons");
    if(extraButtons.is(":visible")){
        // Restore
        extraButtons.hide()
        $("#contact-"+ lineNum +"-action-buttons").css("width", normal+"px");
    } else {
        // Expand
        extraButtons.show()
        $("#contact-"+ lineNum +"-action-buttons").css("width", expanded+"px");
    }
}
function SortBuddies(){

    // Firstly: Type - Second: Last Activity
    if(BuddySortBy == "type"){
        Buddies.sort(function(a, b){
            var aMo = moment.utc(a.lastActivity.replace(" UTC", ""));
            var bMo = moment.utc(b.lastActivity.replace(" UTC", ""));
            // contact | extension | (group) | xmpp
            var aType = a.type;
            var bType = b.type;
            // No groups for now
            if(SortByTypeOrder == "c|e|x") {
                if(a.type == "contact") aType = "A";
                if(b.type == "contact") bType = "A";
                if(a.type == "extension") aType = "B";
                if(b.type == "extension") bType = "B";
                if(a.type == "xmpp") aType = "C";
                if(b.type == "xmpp") bType = "C";
            }
            if(SortByTypeOrder == "c|x|e") {
                if(a.type == "contact") aType = "A";
                if(b.type == "contact") bType = "A";
                if(a.type == "extension") aType = "C";
                if(b.type == "extension") bType = "C";
                if(a.type == "xmpp") aType = "B";
                if(b.type == "xmpp") bType = "B";
            }
            if(SortByTypeOrder == "x|e|c") {
                if(a.type == "contact") aType = "C";
                if(b.type == "contact") bType = "C";
                if(a.type == "extension") aType = "B";
                if(b.type == "extension") bType = "B";
                if(a.type == "xmpp") aType = "A";
                if(b.type == "xmpp") bType = "A";
            }
            if(SortByTypeOrder == "x|c|e") {
                if(a.type == "contact") aType = "B";
                if(b.type == "contact") bType = "B";
                if(a.type == "extension") aType = "C";
                if(b.type == "extension") bType = "C";
                if(a.type == "xmpp") aType = "A";
                if(b.type == "xmpp") bType = "A";
            }
            if(SortByTypeOrder == "e|x|c") {
                if(a.type == "contact") aType = "C";
                if(b.type == "contact") bType = "C";
                if(a.type == "extension") aType = "A";
                if(b.type == "extension") bType = "A";
                if(a.type == "xmpp") aType = "B";
                if(b.type == "xmpp") bType = "B";
            }
            if(SortByTypeOrder == "e|c|x") {
                if(a.type == "contact") aType = "B";
                if(b.type == "contact") bType = "A";
                if(a.type == "extension") aType = "A";
                if(b.type == "extension") bType = "A";
                if(a.type == "xmpp") aType = "C";
                if(b.type == "xmpp") bType = "C";
            }

            return (aType.localeCompare(bType) || (aMo.isSameOrAfter(bMo, "second")? -1 : 1));
        });
    }

    // Extension Number (or Contact Number) - Second: Last Activity
    if(BuddySortBy == "extension"){
        Buddies.sort(function(a, b){
            var aSortBy = (a.type == "extension" || a.type == "xmpp")? a.ExtNo : a.ContactNumber1;
            var bSortBy = (b.type == "extension" || b.type == "xmpp")? b.ExtNo : a.ContactNumber1;
            var aMo = moment.utc(a.lastActivity.replace(" UTC", ""));
            var bMo = moment.utc(b.lastActivity.replace(" UTC", ""));
            return (aSortBy.localeCompare(bSortBy) || (aMo.isSameOrAfter(bMo, "second")? -1 : 1));
        });
    }

    // Name Alphabetically - Second: Last Activity
    if(BuddySortBy == "alphabetical"){
        Buddies.sort(function(a, b){
            var aMo = moment.utc(a.lastActivity.replace(" UTC", ""));
            var bMo = moment.utc(b.lastActivity.replace(" UTC", ""));
            return (a.CallerIDName.localeCompare(b.CallerIDName) || (aMo.isSameOrAfter(bMo, "second")? -1 : 1));
        });
    }

    // Last Activity Only
    if(BuddySortBy == "activity"){
        Buddies.sort(function(a, b){
            var aMo = moment.utc(a.lastActivity.replace(" UTC", ""));
            var bMo = moment.utc(b.lastActivity.replace(" UTC", ""));
            return (aMo.isSameOrAfter(bMo, "second")? -1 : 1);
        });
    }

    // Second Sorts

    // Sort Auto Delete
    if(BuddyAutoDeleteAtEnd == true){
        Buddies.sort(function(a, b){
            return (a.AllowAutoDelete === b.AllowAutoDelete)? 0 : a.AllowAutoDelete? 1 : -1;
        });
    }
    // Sort Out Pinned
    Buddies.sort(function(a, b){
        return (a.Pinned === b.Pinned)? 0 : a.Pinned? -1 : 1;
    });

}


function SelectBuddy(buddy) {
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null) return;

    var displayName = (BuddyShowExtenNum == true && (buddyObj.type == "extension" || buddyObj.type == "xmpp"))? " "+ buddyObj.ExtNo + " - " + buddyObj.CallerIDName : buddyObj.CallerIDName;
    $("#contact-" + buddyObj.identity + "-name").html(displayName);
    var presence = "";
    if(buddyObj.type == "extension"){
        presence += buddyObj.presence;
        if(presence == "Unknown") presence = lang.state_unknown;
        if(presence == "Not online") presence = lang.state_not_online;
        if(presence == "Ready") presence = lang.state_ready;
        if(presence == "On the phone") presence = lang.state_on_the_phone;
        if(presence == "Ringing") presence = lang.state_ringing;
        if(presence == "On hold") presence = lang.state_on_hold;
        if(presence == "Unavailable") presence = lang.state_unavailable;
        if(buddyObj.EnableSubscribe != true) presence = buddyObj.Desc;
    } else if(buddyObj.type == "xmpp"){
        presence += "<i class=\"fa fa-comments\"></i> ";
        presence += buddyObj.presenceText;
    } else if(buddyObj.type == "contact"){
        presence += buddyObj.Desc;
    } else if(buddyObj.type == "group"){
        presence += buddyObj.Desc;
    }
    $("#contact-" + buddyObj.identity + "-presence-main").html(presence);

    $("#contact-"+ buddyObj.identity +"-picture-main").css("background-image", $("#contact-"+ buddyObj.identity +"-picture-main").css("background-image"));

    for(var b = 0; b < Buddies.length; b++) {
        if(Buddies[b].IsSelected == true && Buddies[b].identity == buddy){
            // Nothing to do, you re-selected the same buddy;
            return;
        }
    }

    console.log("Selecting Buddy: "+ buddyObj.CallerIDName);

    selectedBuddy = buddyObj;

    // Can only display one thing on the Right
    $(".streamSelected").each(function () {
        $(this).prop('class', 'stream');
    });
    $("#stream-" + buddy).prop('class', 'streamSelected');

    // Update Lines List
    for(var l = 0; l < Lines.length; l++) {
        var classStr = "buddy";
        if(Lines[l].SipSession != null) classStr = (Lines[l].SipSession.isOnHold)? "buddyActiveCallHollding" : "buddyActiveCall";
        $("#line-" + Lines[l].LineNumber).prop('class', classStr);
        Lines[l].IsSelected = false;
    }

    ClearMissedBadge(buddy);
    // Update Buddy List
    for(var b = 0; b < Buddies.length; b++) {
        var classStr = (Buddies[b].identity == buddy)? "buddySelected" : "buddy";
        $("#contact-" + Buddies[b].identity).prop('class', classStr);

        $("#contact-"+ Buddies[b].identity +"-ChatHistory").empty();

        Buddies[b].IsSelected = (Buddies[b].identity == buddy);
    }

    // Change to Stream if in Narrow view
    UpdateUI();

    // Refresh Stream
    // console.log("Refreshing Stream for you(" + profileUserID + ") and : " + buddyObj.identity);
    RefreshStream(buddyObj);

    try{
        $("#contact-" + buddy).get(0).scrollIntoViewIfNeeded();
    } catch(e){}

    // Save Selected
    localDB.setItem("SelectedBuddy", buddy);
}
function CloseBuddy(buddy){
    // Lines and Buddies (Left)
    $(".buddySelected").each(function () {
        $(this).prop('class', 'buddy');
    });
    // Streams (Right)
    $(".streamSelected").each(function () {
        $(this).prop('class', 'stream');
    });

    console.log("Closing Buddy: "+ buddy);
    for(var b = 0; b < Buddies.length; b++){
        Buddies[b].IsSelected = false;
    }
    selectedBuddy = null;
    for(var l = 0; l < Lines.length; l++){
        Lines[l].IsSelected = false;
    }
    selectedLine = null;

    // Save Selected
    localDB.setItem("SelectedBuddy", null);

    // Change to Stream if in Narrow view
    UpdateUI();
}
function RemoveBuddy(buddy){
    // Check if you are on the phone etc

    CloseWindow();

    Confirm(lang.confirm_remove_buddy, lang.remove_buddy, function(){
        DoRemoveBuddy(buddy)
        UpdateBuddyList();
    });
}
function DoRemoveBuddy(buddy){
    for(var b = 0; b < Buddies.length; b++) {
        if(Buddies[b].identity == buddy) {
            RemoveBuddyMessageStream(Buddies[b]);
            UnsubscribeBuddy(Buddies[b]);
            if(Buddies[b].type == "xmpp") XmppRemoveBuddyFromRoster(Buddies[b]);
            Buddies.splice(b, 1);
            break;
        }
    }
}
function FindBuddyByDid(did){
    // Used only in Inbound
    for(var b = 0; b < Buddies.length; b++){
        if(Buddies[b].ExtNo == did || Buddies[b].MobileNumber == did || Buddies[b].ContactNumber1 == did || Buddies[b].ContactNumber2 == did) {
            return Buddies[b];
        }
    }
    return null;
}
function FindBuddyByExtNo(ExtNo){
    for(var b = 0; b < Buddies.length; b++){
        if(Buddies[b].ExtNo == ExtNo) return Buddies[b];
    }
    return null;
}
function FindBuddyByNumber(number){
    // Number could be: +XXXXXXXXXX
    // Any special characters must be removed prior to adding
    for(var b = 0; b < Buddies.length; b++){
        if(Buddies[b].MobileNumber == number || Buddies[b].ContactNumber1 == number || Buddies[b].ContactNumber2 == number) {
            return Buddies[b];
        }
    }
    return null;
}
function FindBuddyByIdentity(identity){
    for(var b = 0; b < Buddies.length; b++){
        if(Buddies[b].identity == identity) return Buddies[b];
    }
    return null;
}
function FindBuddyByJid(jid){
    for(var b = 0; b < Buddies.length; b++){
        if(Buddies[b].jid == jid) return Buddies[b];
    }
    console.warn("Buddy not found on jid: "+ jid);
    return null;
}
function FindBuddyByObservedUser(SubscribeUser){
    for(var b = 0; b < Buddies.length; b++){
        if(Buddies[b].SubscribeUser == SubscribeUser) return Buddies[b];
    }
    return null;
}

function SearchStream(obj, buddy){
    var q = obj.value;

    var buddyObj = FindBuddyByIdentity(buddy);
    if(q == ""){
        console.log("Restore Stream");
        RefreshStream(buddyObj);
    }
    else{
        RefreshStream(buddyObj, q);
    }
}
function RefreshStream(buddyObj, filter) {
    $("#contact-" + buddyObj.identity + "-ChatHistory").empty();

    var json = JSON.parse(localDB.getItem(buddyObj.identity +"-stream"));
    if(json == null || json.DataCollection == null) return;

    // Sort DataCollection (Newest items first)
    json.DataCollection.sort(function(a, b){
        var aMo = moment.utc(a.ItemDate.replace(" UTC", ""));
        var bMo = moment.utc(b.ItemDate.replace(" UTC", ""));
        if (aMo.isSameOrAfter(bMo, "second")) {
            return -1;
        } else return 1;
        return 0;
    });

    // Filter
    if(filter && filter != ""){
        // TODO: Maybe some room for improvement here
        console.log("Rows without filter ("+ filter +"): ", json.DataCollection.length);
        json.DataCollection = json.DataCollection.filter(function(item){
            if(filter.indexOf("date: ") != -1){
                // Apply Date Filter
                var dateFilter = getFilter(filter, "date");
                if(dateFilter != "" && item.ItemDate.indexOf(dateFilter) != -1) return true;
            }
            if(item.MessageData && item.MessageData.length > 1){
                if(item.MessageData.toLowerCase().indexOf(filter.toLowerCase()) != -1) return true;
                if(filter.toLowerCase().indexOf(item.MessageData.toLowerCase()) != -1) return true;
            }
            if (item.ItemType == "MSG") {
                // Special search??
            }
            else if (item.ItemType == "CDR") {
                // Tag Search
                if(item.Tags && item.Tags.length > 1){
                    var tagFilter = getFilter(filter, "tag");
                    if(tagFilter != "") {
                        if(item.Tags.some(function(i){
                            if(tagFilter.toLowerCase().indexOf(i.value.toLowerCase()) != -1) return true;
                            if(i.value.toLowerCase().indexOf(tagFilter.toLowerCase()) != -1) return true;
                            return false;
                        }) == true) return true;
                    }
                }
            }
            else if(item.ItemType == "FILE"){
                // Not yest implemented
            }
            else if(item.ItemType == "SMS"){
                // Not yest implemented
            }
            // return true to keep;
            return false;
        });
        console.log("Rows After Filter: ", json.DataCollection.length);
    }

    // Create Buffer
    if(json.DataCollection.length > StreamBuffer){
        console.log("Rows:", json.DataCollection.length, " (will be trimmed to "+ StreamBuffer +")");
        // Always limit the Stream to {StreamBuffer}, users much search for messages further back
        json.DataCollection.splice(StreamBuffer);
    }

    $.each(json.DataCollection, function (i, item) {

        var IsToday = moment.utc(item.ItemDate.replace(" UTC", "")).isSame(moment.utc(), "day");
        var DateTime = moment.utc(item.ItemDate.replace(" UTC", "")).local().calendar(null, { sameElse: DisplayDateFormat });
        if(IsToday) DateTime = moment.utc(item.ItemDate.replace(" UTC", "")).local().format(DisplayTimeFormat);

        if (item.ItemType == "MSG") {
            // Add Chat Message
            // ===================

            //Billsec: "0"
            //Dst: "sip:800"
            //DstUserId: "8D68C1D442A96B4"
            //ItemDate: "2019-05-14 09:42:15"
            //ItemId: "89"
            //ItemType: "MSG"
            //MessageData: "........."
            //Src: ""Keyla James" <100>"
            //SrcUserId: "8D68B3EFEC8D0F5"

            var deliveryStatus = "<i class=\"fa fa-question-circle-o SendingMessage\"></i>"
            if(item.Sent == true) deliveryStatus = "<i class=\"fa fa-check SentMessage\"></i>";
            if(item.Sent == false) deliveryStatus = "<i class=\"fa fa-exclamation-circle FailedMessage\"></i>";
            if(item.Delivered && item.Delivered.state == true) {
                deliveryStatus += " <i class=\"fa fa-check DeliveredMessage\"></i>";
            }
            if(item.Displayed && item.Displayed.state == true){
                deliveryStatus = "<i class=\"fa fa-check CompletedMessage\"></i>";
            }

            var formattedMessage = ReformatMessage(item.MessageData);
            var longMessage = (formattedMessage.length > 1000);

            if (item.SrcUserId == profileUserID) {
                // You are the source (sending)
                var messageString = "<table class=ourChatMessage cellspacing=0 cellpadding=0><tr>"
                messageString += "<td class=ourChatMessageText onmouseenter=\"ShowChatMenu(this)\" onmouseleave=\"HideChatMenu(this)\">"
                messageString += "<span onclick=\"ShowMessageMenu(this,'MSG','"+  item.ItemId +"', '"+ buddyObj.identity +"')\" class=chatMessageDropdown style=\"display:none\"><i class=\"fa fa-chevron-down\"></i></span>";
                messageString += "<div id=msg-text-"+ item.ItemId +" class=messageText style=\""+ ((longMessage)? "max-height:190px; overflow:hidden" : "") +"\">" + formattedMessage + "</div>"
                if(longMessage){
                    messageString += "<div id=msg-readmore-"+  item.ItemId +" class=messageReadMore><span onclick=\"ExpandMessage(this,'"+ item.ItemId +"', '"+ buddyObj.identity +"')\">"+ lang.read_more +"</span></div>"
                }
                messageString += "<div class=messageDate>" + DateTime + " " + deliveryStatus +"</div>"
                messageString += "</td>"
                messageString += "</tr></table>";
            }
            else {
                // You are the destination (receiving)
                var ActualSender = ""; //TODO
                var messageString = "<table class=theirChatMessage cellspacing=0 cellpadding=0><tr>"
                messageString += "<td class=theirChatMessageText onmouseenter=\"ShowChatMenu(this)\" onmouseleave=\"HideChatMenu(this)\">";
                messageString += "<span onclick=\"ShowMessageMenu(this,'MSG','"+  item.ItemId +"', '"+ buddyObj.identity +"')\" class=chatMessageDropdown style=\"display:none\"><i class=\"fa fa-chevron-down\"></i></span>";
                if(buddyObj.type == "group"){
                    messageString += "<div class=messageDate>" + ActualSender + "</div>";
                }
                messageString += "<div id=msg-text-"+ item.ItemId +" class=messageText style=\""+ ((longMessage)? "max-height:190px; overflow:hidden" : "") +"\">" + formattedMessage + "</div>";
                if(longMessage){
                    messageString += "<div id=msg-readmore-"+  item.ItemId +" class=messageReadMore><span onclick=\"ExpandMessage(this,'"+ item.ItemId +"', '"+ buddyObj.identity +"')\">"+ lang.read_more +"</span></div>"
                }
                messageString += "<div class=messageDate>"+ DateTime + "</div>";
                messageString += "</td>";
                messageString += "</tr></table>";

                // Update any received messages
                if(buddyObj.type == "xmpp") {
                    var streamVisible = $("#stream-"+ buddyObj.identity).is(":visible");
                    if (streamVisible && !item.Read) {
                        console.log("Buddy stream is now visible, marking XMPP message("+ item.ItemId +") as read")
                        MarkMessageRead(buddyObj, item.ItemId);
                        XmppSendDisplayReceipt(buddyObj, item.ItemId);
                    }
                }

            }
            $("#contact-" + buddyObj.identity + "-ChatHistory").prepend(messageString);
        }
        else if (item.ItemType == "CDR") {
            // Add CDR
            // =======

            // CdrId = uID(),
            // ItemType: "CDR",
            // ItemDate: "...",
            // SrcUserId: srcId,
            // Src: srcCallerID,
            // DstUserId: dstId,
            // Dst: dstCallerID,
            // Billsec: duration.asSeconds(),
            // MessageData: ""
            // ReasonText:
            // ReasonCode:
            // Flagged
            // Tags: [""", "", "", ""]
            // Transfers: [{}],
            // Mutes: [{}],
            // Holds: [{}],
            // Recordings: [{ uID, startTime, mediaType, stopTime: utcDateNow, size}],
            // QOS: [{}]

            var iconColor = (item.Billsec > 0)? "green" : "red";
            var formattedMessage = "";

            // Flagged
            var flag = "<span id=cdr-flagged-"+  item.CdrId +" style=\""+ ((item.Flagged)? "" : "display:none") +"\">";
            flag += "<i class=\"fa fa-flag FlagCall\"></i> ";
            flag += "</span>";

            // Comment
            var callComment = "";
            if(item.MessageData) callComment = item.MessageData;

            // Tags
            if(!item.Tags) item.Tags = [];
            var CallTags = "<ul id=cdr-tags-"+  item.CdrId +" class=tags style=\""+ ((item.Tags && item.Tags.length > 0)? "" : "display:none" ) +"\">"
            $.each(item.Tags, function (i, tag) {
                CallTags += "<li onclick=\"TagClick(this, '"+ item.CdrId +"', '"+ buddyObj.identity +"')\">"+ tag.value +"</li>";
            });
            CallTags += "<li class=tagText><input maxlength=24 type=text onkeypress=\"TagKeyPress(event, this, '"+ item.CdrId +"', '"+ buddyObj.identity +"')\" onfocus=\"TagFocus(this)\"></li>";
            CallTags += "</ul>";

            // Call Type
            var callIcon = (item.WithVideo)? "fa-video-camera" :  "fa-phone";
            formattedMessage += "<i class=\"fa "+ callIcon +"\" style=\"color:"+ iconColor +"\"></i>";
            var audioVideo = (item.WithVideo)? lang.a_video_call :  lang.an_audio_call;

            // Recordings
            var recordingsHtml = "";
            if(item.Recordings && item.Recordings.length >= 1){
                $.each(item.Recordings, function (i, recording) {
                    if(recording.uID){
                        var StartTime = moment.utc(recording.startTime.replace(" UTC", "")).local();
                        var StopTime = moment.utc(recording.stopTime.replace(" UTC", "")).local();
                        var recordingDuration = moment.duration(StopTime.diff(StartTime));
                        recordingsHtml += "<div class=callRecording>";
                        if(item.WithVideo){
                            if(recording.Poster){
                                var posterWidth = recording.Poster.width;
                                var posterHeight = recording.Poster.height;
                                var posterImage = recording.Poster.posterBase64;
                                recordingsHtml += "<div><IMG src=\""+ posterImage +"\"><button onclick=\"PlayVideoCallRecording(this, '"+ item.CdrId +"', '"+ recording.uID +"')\" class=videoPoster><i class=\"fa fa-play\"></i></button></div>";
                            }
                            else {
                                recordingsHtml += "<div><button class=roundButtons onclick=\"PlayVideoCallRecording(this, '"+ item.CdrId +"', '"+ recording.uID +"', '"+ buddyObj.identity +"')\"><i class=\"fa fa-video-camera\"></i></button></div>";
                            }
                        }
                        else {
                            recordingsHtml += "<div><button class=roundButtons onclick=\"PlayAudioCallRecording(this, '"+ item.CdrId +"', '"+ recording.uID +"', '"+ buddyObj.identity +"')\"><i class=\"fa fa-play\"></i></button></div>";
                        }
                        recordingsHtml += "<div>"+ lang.started +": "+ StartTime.format(DisplayTimeFormat) +" <i class=\"fa fa-long-arrow-right\"></i> "+ lang.stopped +": "+ StopTime.format(DisplayTimeFormat) +"</div>";
                        recordingsHtml += "<div>"+ lang.recording_duration +": "+ formatShortDuration(recordingDuration.asSeconds()) +"</div>";
                        recordingsHtml += "<div>";
                        recordingsHtml += "<span id=\"cdr-video-meta-width-"+ item.CdrId +"-"+ recording.uID +"\"></span>";
                        recordingsHtml += "<span id=\"cdr-video-meta-height-"+ item.CdrId +"-"+ recording.uID +"\"></span>";
                        recordingsHtml += "<span id=\"cdr-media-meta-size-"+ item.CdrId +"-"+ recording.uID +"\"></span>";
                        recordingsHtml += "<span id=\"cdr-media-meta-codec-"+ item.CdrId +"-"+ recording.uID +"\"></span>";
                        recordingsHtml += "</div>";
                        recordingsHtml += "</div>";
                    }
                });
            }

            if (item.SrcUserId == profileUserID) {
                // (Outbound) You(profileUserID) initiated a call
                if(item.Billsec == "0") {
                    formattedMessage += " "+ lang.you_tried_to_make +" "+ audioVideo +" ("+ item.ReasonText +").";
                }
                else {
                    formattedMessage += " "+ lang.you_made + " "+ audioVideo +", "+ lang.and_spoke_for +" " + formatDuration(item.Billsec) + ".";
                }
                var messageString = "<table class=ourChatMessage cellspacing=0 cellpadding=0><tr>"
                messageString += "<td style=\"padding-right:4px;\">" + flag + "</td>"
                messageString += "<td class=ourChatMessageText onmouseenter=\"ShowChatMenu(this)\" onmouseleave=\"HideChatMenu(this)\">";
                messageString += "<span onClick=\"ShowMessageMenu(this,'CDR','"+  item.CdrId +"', '"+ buddyObj.identity +"')\" class=chatMessageDropdown style=\"display:none\"><i class=\"fa fa-chevron-down\"></i></span>";
                messageString += "<div>" + formattedMessage + "</div>";
                messageString += "<div>" + CallTags + "</div>";
                messageString += "<div id=cdr-comment-"+  item.CdrId +" class=cdrComment>" + callComment + "</div>";
                messageString += "<div class=callRecordings>" + recordingsHtml + "</div>";
                messageString += "<div class=messageDate>" + DateTime  + "</div>";
                messageString += "</td>"
                messageString += "</tr></table>";
            }
            else {
                // (Inbound) you(profileUserID) received a call
                if(item.Billsec == "0"){
                    formattedMessage += " "+ lang.you_missed_a_call + " ("+ item.ReasonText +").";
                }
                else {
                    formattedMessage += " "+ lang.you_received + " "+ audioVideo +", "+ lang.and_spoke_for +" " + formatDuration(item.Billsec) + ".";
                }
                var messageString = "<table class=theirChatMessage cellspacing=0 cellpadding=0><tr>";
                messageString += "<td class=theirChatMessageText onmouseenter=\"ShowChatMenu(this)\" onmouseleave=\"HideChatMenu(this)\">";
                messageString += "<span onClick=\"ShowMessageMenu(this,'CDR','"+  item.CdrId +"', '"+ buddyObj.identity +"')\" class=chatMessageDropdown style=\"display:none\"><i class=\"fa fa-chevron-down\"></i></span>";
                messageString += "<div style=\"text-align:left\">" + formattedMessage + "</div>";
                messageString += "<div>" + CallTags + "</div>";
                messageString += "<div id=cdr-comment-"+  item.CdrId +" class=cdrComment>" + callComment + "</div>";
                messageString += "<div class=callRecordings>" + recordingsHtml + "</div>";
                messageString += "<div class=messageDate> " + DateTime + "</div>";
                messageString += "</td>";
                messageString += "<td style=\"padding-left:4px\">" + flag + "</td>";
                messageString += "</tr></table>";
            }
            // Messages are prepended here, and appended when logging
            $("#contact-" + buddyObj.identity + "-ChatHistory").prepend(messageString);
        }
        else if(item.ItemType == "FILE"){
            // TODO
        }
        else if(item.ItemType == "SMS"){
            // TODO
        }
    });

    // For some reason, the first time this fires, it doesn't always work
    updateScroll(buddyObj.identity);
    window.setTimeout(function(){
        updateScroll(buddyObj.identity);
    }, 300);
}
function ShowChatMenu(obj){
    $(obj).children("span").show();
}
function HideChatMenu(obj){
    $(obj).children("span").hide();
}
function ExpandMessage(obj, ItemId, buddy){
    $("#msg-text-" + ItemId).css("max-height", "");
    $("#msg-text-" + ItemId).css("overflow", "");
    $("#msg-readmore-" + ItemId).remove();

    HidePopup(500);
}

// Video Conference Stage
// ======================
function RedrawStage(lineNum, videoChanged){
    var  stage = $("#line-" + lineNum + "-VideoCall");
    var container = $("#line-" + lineNum + "-stage-container");
    var previewContainer = $("#line-"+  lineNum +"-preview-container");
    var videoContainer = $("#line-" + lineNum + "-remote-videos");

    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null) return;
    var session = lineObj.SipSession;
    if(session == null) return;

    var isVideoPinned = false;
    var pinnedVideoID = "";

    // Preview Area
    previewContainer.find('video').each(function(i, video) {
        $(video).hide();
    });
    previewContainer.css("width",  "");

    // Count and Tag Videos
    var videoCount = 0;
    videoContainer.find('video').each(function(i, video) {
        var thisRemoteVideoStream = video.srcObject;
        var videoTrack = thisRemoteVideoStream.getVideoTracks()[0];
        var videoTrackSettings = videoTrack.getSettings();
        var srcVideoWidth = (videoTrackSettings.width)? videoTrackSettings.width : video.videoWidth;
        var srcVideoHeight = (videoTrackSettings.height)? videoTrackSettings.height : video.videoHeight;

        if(thisRemoteVideoStream.mid) {
            thisRemoteVideoStream.channel = "unknown"; // Asterisk Channel
            thisRemoteVideoStream.CallerIdName = "";
            thisRemoteVideoStream.CallerIdNumber = "";
            thisRemoteVideoStream.isAdminMuted = false;
            thisRemoteVideoStream.isAdministrator = false;
            if(session && session.data && session.data.videoChannelNames){
                session.data.videoChannelNames.forEach(function(videoChannelName){
                    if(thisRemoteVideoStream.mid == videoChannelName.mid){
                        thisRemoteVideoStream.channel = videoChannelName.channel;
                    }
                });
            }
            if(session && session.data && session.data.ConfbridgeChannels){
                session.data.ConfbridgeChannels.forEach(function(ConfbridgeChannel){
                    if(ConfbridgeChannel.id == thisRemoteVideoStream.channel){
                        thisRemoteVideoStream.CallerIdName = ConfbridgeChannel.caller.name;
                        thisRemoteVideoStream.CallerIdNumber = ConfbridgeChannel.caller.number;
                        thisRemoteVideoStream.isAdminMuted = ConfbridgeChannel.muted;
                        thisRemoteVideoStream.isAdministrator = ConfbridgeChannel.admin;
                    }
                });
            }
            // console.log("Track MID :", thisRemoteVideoStream.mid, thisRemoteVideoStream.channel);
        }

        // Remove any in the preview area
        if(videoChanged){
            $("#line-" + lineNum + "-preview-container").find('video').each(function(i, video) {
                if(video.id.indexOf("copy-") == 0){
                    video.remove();
                }
            });
        }

        // Prep Videos
        $(video).parent().off("click");
        $(video).parent().css("width", "1px");
        $(video).parent().css("height", "1px");
        $(video).hide();
        $(video).parent().hide();

        // Count Videos
        if(lineObj.pinnedVideo && lineObj.pinnedVideo == thisRemoteVideoStream.trackID && videoTrack.readyState == "live" && srcVideoWidth > 10 && srcVideoHeight >= 10){
            // A valid and live video is pinned
            isVideoPinned = true;
            pinnedVideoID = lineObj.pinnedVideo;
        }
        // Count All the videos
        if(videoTrack.readyState == "live" && srcVideoWidth > 10 && srcVideoHeight >= 10) {
            videoCount ++;
            console.log("Display Video - ", videoTrack.readyState, "MID:", thisRemoteVideoStream.mid, "channel:", thisRemoteVideoStream.channel, "src width:", srcVideoWidth, "src height", srcVideoHeight);
        }
        else{
            console.log("Hide Video - ", videoTrack.readyState ,"MID:", thisRemoteVideoStream.mid);
        }


    });
    if(videoCount == 0) {
        // If you are the only one in the conference, just display your self
        previewContainer.css("width",  previewWidth +"px");
        previewContainer.find('video').each(function(i, video) {
            $(video).show();
        });
        return;
    }
    if(isVideoPinned) videoCount = 1;

    if(!videoContainer.outerWidth() > 0) return;
    if(!videoContainer.outerHeight() > 0) return;

    // videoAspectRatio (1|1.33|1.77) is for the peer video, so can technically be used here
    // default ia 4:3
    var Margin = 3;
    var videoRatio = 0.750; // 0.5625 = 9/16 (16:9) | 0.75   = 3/4 (4:3)
    if(videoAspectRatio == "" || videoAspectRatio == "1.33") videoRatio = 0.750;
    if(videoAspectRatio == "1.77") videoRatio = 0.5625;
    if(videoAspectRatio == "1") videoRatio = 1;
    var stageWidth = videoContainer.outerWidth() - (Margin * 2);
    var stageHeight = videoContainer.outerHeight() - (Margin * 2);
    var previewWidth = previewContainer.outerWidth();
    var maxWidth = 0;
    let i = 1;
    while (i < 5000) {
        let w = StageArea(i, videoCount, stageWidth, stageHeight, Margin, videoRatio);
        if (w === false) {
            maxWidth =  i - 1;
            break;
        }
        i++;
    }
    maxWidth = maxWidth - (Margin * 2);

    // Layout Videos
    videoContainer.find('video').each(function(i, video) {
        var thisRemoteVideoStream = video.srcObject;
        var videoTrack = thisRemoteVideoStream.getVideoTracks()[0];
        var videoTrackSettings = videoTrack.getSettings();
        var srcVideoWidth = (videoTrackSettings.width)? videoTrackSettings.width : video.videoWidth;
        var srcVideoHeight = (videoTrackSettings.height)? videoTrackSettings.height : video.videoHeight;

        var videoWidth = maxWidth;
        var videoHeight = maxWidth * videoRatio;

        // Set & Show
        if(isVideoPinned){
            // One of the videos are pinned
            if(pinnedVideoID == video.srcObject.trackID){
                $(video).parent().css("width", videoWidth+"px");
                $(video).parent().css("height", videoHeight+"px");
                $(video).show();
                $(video).parent().show();
                // Pinned Actions
                var unPinButton = $("<button />", {
                    class: "videoOverlayButtons",
                });
                unPinButton.html("<i class=\"fa fa-th-large\"></i>");
                unPinButton.on("click", function(){
                    UnPinVideo(lineNum, video);
                });
                $(video).parent().find(".Actions").empty();
                $(video).parent().find(".Actions").append(unPinButton);
            } else {
                // Put the videos in the preview area
                if(videoTrack.readyState == "live" && srcVideoWidth > 10 && srcVideoHeight >= 10) {
                    if(videoChanged){
                        var videoEl = $("<video />", {
                            id: "copy-"+ thisRemoteVideoStream.id,
                            muted: true,
                            autoplay: true,
                            playsinline: true,
                            controls: false
                        });
                        var videoObj = videoEl.get(0);
                        videoObj.srcObject = thisRemoteVideoStream;
                        $("#line-" + lineNum + "-preview-container").append(videoEl);
                    }
                }
            }
        }
        else {
            // None of the videos are pinned
            if(videoTrack.readyState == "live" && srcVideoWidth > 10 && srcVideoHeight >= 10) {
                // Unpinned
                $(video).parent().css("width", videoWidth+"px");
                $(video).parent().css("height", videoHeight+"px");
                $(video).show();
                $(video).parent().show();
                // Unpinned Actions
                var pinButton = $("<button />", {
                    class: "videoOverlayButtons",
                });
                pinButton.html("<i class=\"fa fa-thumb-tack\"></i>");
                pinButton.on("click", function(){
                    PinVideo(lineNum, video, video.srcObject.trackID);
                });
                $(video).parent().find(".Actions").empty();
                if(videoCount > 1){
                    // More then one video, nothing pinned
                    $(video).parent().find(".Actions").append(pinButton);
                }

            }
        }

        // Populate Caller ID
        var adminMuteIndicator = "";
        var administratorIndicator = "";
        if(thisRemoteVideoStream.isAdminMuted == true){
            adminMuteIndicator = "<i class=\"fa fa-microphone-slash\" style=\"color:red\"></i>&nbsp;"
        }
        if(thisRemoteVideoStream.isAdministrator == true){
            administratorIndicator = "<i class=\"fa fa-user\" style=\"color:orange\"></i>&nbsp;"
        }
        if(thisRemoteVideoStream.CallerIdName == ""){
            thisRemoteVideoStream.CallerIdName = FindBuddyByIdentity(session.data.buddyId).CallerIDName;
        }
        $(video).parent().find(".callerID").html(administratorIndicator + adminMuteIndicator + thisRemoteVideoStream.CallerIdName);


    });

    // Preview Area
    previewContainer.css("width",  previewWidth +"px");
    previewContainer.find('video').each(function(i, video) {
        $(video).show();
    });

}
function StageArea(Increment, Count, Width, Height, Margin, videoRatio) {
    // Thanks:  https://github.com/Alicunde/Videoconference-Dish-CSS-JS
    let i = w = 0;
    let h = Increment * videoRatio + (Margin * 2);
    while (i < (Count)) {
        if ((w + Increment) > Width) {
            w = 0;
            h = h + (Increment * videoRatio) + (Margin * 2);
        }
        w = w + Increment + (Margin * 2);
        i++;
    }
    if (h > Height) return false;
    else return Increment;
}
function PinVideo(lineNum, videoEl, trackID){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null) return;

    console.log("Setting Pinned Video:", trackID);
    lineObj.pinnedVideo = trackID;
    videoEl.srcObject.isPinned = true;
    RedrawStage(lineNum, true);
}
function UnPinVideo(lineNum, videoEl){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null) return;

    console.log("Removing Pinned Video");
    lineObj.pinnedVideo = "";
    videoEl.srcObject.isPinned = false;
    RedrawStage(lineNum, true);
}


// Stream Functionality
// =====================
function ShowMessageMenu(obj, typeStr, cdrId, buddy) {

    var items = [];
    if (typeStr == "CDR") {
        var TagState = $("#cdr-flagged-"+ cdrId).is(":visible");
        var TagText = (TagState)? lang.clear_flag : lang.flag_call;

        items.push({ value: 1, icon: "fa fa-external-link", text: lang.show_call_detail_record });
        items.push({ value: 2, icon: "fa fa-tags", text: lang.tag_call });
        items.push({ value: 3, icon: "fa fa-flag", text: TagText });
        items.push({ value: 4, icon: "fa fa-quote-left", text: lang.edit_comment });
        // items.push({ value: 20, icon: null, text: "Delete CDR" });
        // items.push({ value: 21, icon: null, text: "Remove Poster Images" });
    }
    else if (typeStr == "MSG") {
        items.push({ value: 10, icon: "fa fa-clipboard", text: lang.copy_message });
        // items.push({ value: 11, icon: "fa fa-pencil", text: "Edit Message" });
        items.push({ value: 12, icon: "fa fa-quote-left", text: lang.quote_message });
    }

    var menu = {
        selectEvent : function( event, ui ) {
            var id = ui.item.attr("value");
            HidePopup();

            if(id != null) {
                console.log("Menu click ("+ id +")");

                // CDR messages
                if(id == 1){

                    var cdr = null;
                    var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
                    if(currentStream != null || currentStream.DataCollection != null){
                        $.each(currentStream.DataCollection, function (i, item) {
                            if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                                // Found
                                cdr = item;
                                return false;
                            }
                        });
                    }
                    if(cdr == null) return;

                    var callDetails = [];
                    var html = "<div class=\"UiWindowField\">";

                    // Billsec: 2.461
                    // CallAnswer: "2020-06-22 09:47:52 UTC" | null
                    // CallDirection: "outbound"
                    // CallEnd: "2020-06-22 09:47:54 UTC"
                    // CdrId: "15928192748351E9D"
                    // ConfCalls: [{…}]
                    // Dst: "*65"
                    // DstUserId: "15919450411467CC"
                    // Holds: [{…}]
                    // ItemDate: "2020-06-22 09:47:50 UTC"
                    // ItemType: "CDR"
                    // MessageData: null
                    // Mutes: [{…}]
                    // QOS: [{…}]
                    // ReasonCode: 16
                    // ReasonText: "Normal Call clearing"
                    // Recordings: [{…}]
                    // RingTime: 2.374
                    // SessionId: "67sv8o86msa7df23"
                    // Src: "<100> Conrad de Wet"
                    // SrcUserId: "17186D5983F"
                    // Tags: [{…}]
                    // Terminate: "us"
                    // TotalDuration: 4.835
                    // Transfers: [{…}]
                    // WithVideo: false

                    var CallDate = moment.utc(cdr.ItemDate.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat);
                    var CallAnswer = (cdr.CallAnswer)? moment.utc(cdr.CallAnswer.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat) : null ;
                    var ringTime = (cdr.RingTime)? cdr.RingTime : 0 ;
                    var CallEnd = moment.utc(cdr.CallEnd.replace(" UTC", "")).local().format(DisplayDateFormat +" "+ DisplayTimeFormat);

                    var srcCallerID = "";
                    var dstCallerID = "";
                    if(cdr.CallDirection == "inbound") {
                        srcCallerID = cdr.Src;
                    }
                    else if(cdr.CallDirection == "outbound") {
                        dstCallerID = cdr.Dst;
                    }
                    html += "<div class=UiText><b>SIP CallID</b> : "+ cdr.SessionId +"</div>";
                    html += "<div class=UiText><b>"+ lang.call_direction +"</b> : "+ cdr.CallDirection +"</div>";
                    html += "<div class=UiText><b>"+ lang.call_date_and_time +"</b> : "+ CallDate +"</div>";
                    html += "<div class=UiText><b>"+ lang.ring_time +"</b> : "+ formatDuration(ringTime) +" ("+ ringTime +")</div>";
                    html += "<div class=UiText><b>"+ lang.talk_time +"</b> : " + formatDuration(cdr.Billsec) +" ("+ cdr.Billsec +")</div>";
                    html += "<div class=UiText><b>"+ lang.call_duration +"</b> : "+ formatDuration(cdr.TotalDuration) +" ("+ cdr.TotalDuration +")</div>";
                    html += "<div class=UiText><b>"+ lang.video_call +"</b> : "+ ((cdr.WithVideo)? lang.yes : lang.no) +"</div>";
                    html += "<div class=UiText><b>"+ lang.flagged +"</b> : "+ ((cdr.Flagged)? "<i class=\"fa fa-flag FlagCall\"></i> " + lang.yes : lang.no)  +"</div>";
                    html += "<hr>";
                    html += "<h2 style=\"font-size: 16px\">"+ lang.call_tags +"</h2>";
                    html += "<hr>";
                    $.each(cdr.Tags, function(item, tag){
                        html += "<span class=cdrTag>"+ tag.value +"</span>"
                    });

                    html += "<h2 style=\"font-size: 16px\">"+ lang.call_notes +"</h2>";
                    html += "<hr>";
                    if(cdr.MessageData){
                        html += "\"" + cdr.MessageData + "\"";
                    }

                    html += "<h2 style=\"font-size: 16px\">"+ lang.activity_timeline +"</h2>";
                    html += "<hr>";

                    var withVideo = (cdr.WithVideo)? "("+ lang.with_video +")" : "";
                    var startCallMessage = (cdr.CallDirection == "inbound")? lang.you_received_a_call_from + " " + srcCallerID  +" "+ withVideo : lang.you_made_a_call_to + " " + dstCallerID +" "+ withVideo;
                    callDetails.push({
                        Message: startCallMessage,
                        TimeStr: cdr.ItemDate
                    });
                    if(CallAnswer){
                        var answerCallMessage = (cdr.CallDirection == "inbound")? lang.you_answered_after + " " + ringTime + " " + lang.seconds_plural : lang.they_answered_after + " " + ringTime + " " + lang.seconds_plural;
                        callDetails.push({
                            Message: answerCallMessage,
                            TimeStr: cdr.CallAnswer
                        });
                    }
                    $.each(cdr.Transfers, function(item, transfer){
                        var msg = (transfer.type == "Blind")? lang.you_started_a_blind_transfer_to +" "+ transfer.to +". " : lang.you_started_an_attended_transfer_to + " "+ transfer.to +". ";
                        if(transfer.accept && transfer.accept.complete == true){
                            msg += lang.the_call_was_completed
                        }
                        else if(transfer.accept.disposition != "") {
                            msg += lang.the_call_was_not_completed +" ("+ transfer.accept.disposition +")"
                        }
                        callDetails.push({
                            Message : msg,
                            TimeStr : transfer.transferTime
                        });
                    });
                    $.each(cdr.Mutes, function(item, mute){
                        callDetails.push({
                            Message : (mute.event == "mute")? lang.you_put_the_call_on_mute : lang.you_took_the_call_off_mute,
                            TimeStr : mute.eventTime
                        });
                    });
                    $.each(cdr.Holds, function(item, hold){
                        callDetails.push({
                            Message : (hold.event == "hold")? lang.you_put_the_call_on_hold : lang.you_took_the_call_off_hold,
                            TimeStr : hold.eventTime
                        });
                    });
                    $.each(cdr.ConfbridgeEvents, function(item, event){
                        callDetails.push({
                            Message : event.event,
                            TimeStr : event.eventTime
                        });
                    });
                    $.each(cdr.ConfCalls, function(item, confCall){
                        var msg = lang.you_started_a_conference_call_to +" "+ confCall.to +". ";
                        if(confCall.accept && confCall.accept.complete == true){
                            msg += lang.the_call_was_completed
                        }
                        else if(confCall.accept.disposition != "") {
                            msg += lang.the_call_was_not_completed +" ("+ confCall.accept.disposition +")"
                        }
                        callDetails.push({
                            Message : msg,
                            TimeStr : confCall.startTime
                        });
                    });
                    $.each(cdr.Recordings, function(item, recording){
                        var StartTime = moment.utc(recording.startTime.replace(" UTC", "")).local();
                        var StopTime = moment.utc(recording.stopTime.replace(" UTC", "")).local();
                        var recordingDuration = moment.duration(StopTime.diff(StartTime));

                        var msg = lang.call_is_being_recorded;
                        if(recording.startTime != recording.stopTime){
                            msg += "("+ formatShortDuration(recordingDuration.asSeconds()) +")"
                        }
                        callDetails.push({
                            Message : msg,
                            TimeStr : recording.startTime
                        });
                    });
                    callDetails.push({
                        Message: (cdr.Terminate == "us")? lang.you_ended_the_call : lang.they_ended_the_call,
                        TimeStr : cdr.CallEnd
                    });

                    callDetails.sort(function(a, b){
                        var aMo = moment.utc(a.TimeStr.replace(" UTC", ""));
                        var bMo = moment.utc(b.TimeStr.replace(" UTC", ""));
                        if (aMo.isSameOrAfter(bMo, "second")) {
                            return 1;
                        } else return -1;
                        return 0;
                    });
                    $.each(callDetails, function(item, detail){
                        var Time = moment.utc(detail.TimeStr.replace(" UTC", "")).local().format(DisplayTimeFormat);
                        var messageString = "<table class=timelineMessage cellspacing=0 cellpadding=0><tr>"
                        messageString += "<td class=timelineMessageArea>"
                        messageString += "<div class=timelineMessageDate style=\"color: #333333\"><i class=\"fa fa-circle timelineMessageDot\"></i>"+ Time +"</div>"
                        messageString += "<div class=timelineMessageText style=\"color: #000000\">"+ detail.Message +"</div>"
                        messageString += "</td>"
                        messageString += "</tr></table>";
                        html += messageString;
                    });

                    html += "<h2 style=\"font-size: 16px\">"+ lang.call_recordings +"</h2>";
                    html += "<hr>";
                    var recordingsHtml = "";
                    $.each(cdr.Recordings, function(r, recording){
                        if(recording.uID){
                            var StartTime = moment.utc(recording.startTime.replace(" UTC", "")).local();
                            var StopTime = moment.utc(recording.stopTime.replace(" UTC", "")).local();
                            var recordingDuration = moment.duration(StopTime.diff(StartTime));
                            recordingsHtml += "<div>";
                            if(cdr.WithVideo){
                                recordingsHtml += "<div><video id=\"callrecording-video-"+ recording.uID +"\" controls playsinline style=\"width: 100%\"></div>";
                            }
                            else {
                                recordingsHtml += "<div><audio id=\"callrecording-audio-"+ recording.uID +"\" controls style=\"width: 100%\"></div>";
                            }
                            recordingsHtml += "<div>"+ lang.started +": "+ StartTime.format(DisplayTimeFormat) +" <i class=\"fa fa-long-arrow-right\"></i> "+ lang.stopped +": "+ StopTime.format(DisplayTimeFormat) +"</div>";
                            recordingsHtml += "<div>"+ lang.recording_duration +": "+ formatShortDuration(recordingDuration.asSeconds()) +"</div>";
                            recordingsHtml += "<div><a id=\"download-"+ recording.uID +"\">"+ lang.save_as +"</a> ("+ lang.right_click_and_select_save_link_as +")</div>";
                            recordingsHtml += "</div>";
                        }
                    });
                    html += recordingsHtml;
                    if(cdr.CallAnswer) {
                        html += "<h2 style=\"font-size: 16px\">"+ lang.send_statistics +"</h2>";
                        html += "<hr>";
                        html += "<div style=\"position: relative; margin: auto; height: 160px; width: 100%;\"><canvas id=\"cdr-AudioSendBitRate\"></canvas></div>";
                        html += "<div style=\"position: relative; margin: auto; height: 160px; width: 100%;\"><canvas id=\"cdr-AudioSendPacketRate\"></canvas></div>";

                        html += "<h2 style=\"font-size: 16px\">"+ lang.receive_statistics +"</h2>";
                        html += "<hr>";
                        html += "<div style=\"position: relative; margin: auto; height: 160px; width: 100%;\"><canvas id=\"cdr-AudioReceiveBitRate\"></canvas></div>";
                        html += "<div style=\"position: relative; margin: auto; height: 160px; width: 100%;\"><canvas id=\"cdr-AudioReceivePacketRate\"></canvas></div>";
                        html += "<div style=\"position: relative; margin: auto; height: 160px; width: 100%;\"><canvas id=\"cdr-AudioReceivePacketLoss\"></canvas></div>";
                        html += "<div style=\"position: relative; margin: auto; height: 160px; width: 100%;\"><canvas id=\"cdr-AudioReceiveJitter\"></canvas></div>";
                        html += "<div style=\"position: relative; margin: auto; height: 160px; width: 100%;\"><canvas id=\"cdr-AudioReceiveLevels\"></canvas></div>";
                    }

                    html += "<br><br></div>";
                    OpenWindow(html, lang.call_detail_record, 480, 640, false, true, null, null, lang.cancel, function(){
                        CloseWindow();
                    }, function(){
                        // Queue video and audio
                        $.each(cdr.Recordings, function(r, recording){
                            var mediaObj = null;
                            if(cdr.WithVideo){
                                mediaObj = $("#callrecording-video-"+ recording.uID).get(0);
                            }
                            else {
                                mediaObj = $("#callrecording-audio-"+ recording.uID).get(0);
                            }
                            var downloadURL = $("#download-"+ recording.uID);

                            // Playback device
                            var sinkId = getAudioOutputID();
                            if (typeof mediaObj.sinkId !== 'undefined') {
                                mediaObj.setSinkId(sinkId).then(function(){
                                    console.log("sinkId applied: "+ sinkId);
                                }).catch(function(e){
                                    console.warn("Error using setSinkId: ", e);
                                });
                            } else {
                                console.warn("setSinkId() is not possible using this browser.")
                            }

                            // Get Call Recording
                            var indexedDB = window.indexedDB;
                            var request = indexedDB.open("CallRecordings", 1);
                            request.onerror = function(event) {
                                console.error("IndexDB Request Error:", event);
                            }
                            request.onupgradeneeded = function(event) {
                                console.warn("Upgrade Required for IndexDB... probably because of first time use.");
                            }
                            request.onsuccess = function(event) {
                                console.log("IndexDB connected to CallRecordings");

                                var IDB = event.target.result;
                                if(IDB.objectStoreNames.contains("Recordings") == false){
                                    console.warn("IndexDB CallRecordings.Recordings does not exists");
                                    return;
                                }

                                var transaction = IDB.transaction(["Recordings"]);
                                var objectStoreGet = transaction.objectStore("Recordings").get(recording.uID);
                                objectStoreGet.onerror = function(event) {
                                    console.error("IndexDB Get Error:", event);
                                }
                                objectStoreGet.onsuccess = function(event) {
                                    var mediaBlobUrl = window.URL.createObjectURL(event.target.result.mediaBlob);
                                    mediaObj.src = mediaBlobUrl;

                                    // Download Link
                                    if(cdr.WithVideo){
                                        downloadURL.prop("download",  "Video-Call-Recording-"+ recording.uID +".webm");
                                    }
                                    else {
                                        downloadURL.prop("download",  "Audio-Call-Recording-"+ recording.uID +".webm");
                                    }
                                    downloadURL.prop("href", mediaBlobUrl);
                                }
                            }

                        });

                        // Display QOS data
                        if(cdr.CallAnswer) DisplayQosData(cdr.SessionId);
                    });
                }
                if(id == 2){
                    $("#cdr-tags-"+ cdrId).show();
                }
                if(id == 3){
                    // Tag / Untag Call
                    var TagState = $("#cdr-flagged-"+ cdrId).is(":visible");
                    if(TagState){
                        console.log("Clearing Flag from: ", cdrId);
                        $("#cdr-flagged-"+ cdrId).hide();

                        // Update DB
                        var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
                        if(currentStream != null || currentStream.DataCollection != null){
                            $.each(currentStream.DataCollection, function (i, item) {
                                if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                                    // Found
                                    item.Flagged = false;
                                    return false;
                                }
                            });
                            localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));
                        }
                    }
                    else {
                        console.log("Flag Call: ", cdrId);
                        $("#cdr-flagged-"+ cdrId).show();

                        // Update DB
                        var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
                        if(currentStream != null || currentStream.DataCollection != null){
                            $.each(currentStream.DataCollection, function (i, item) {
                                if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                                    // Found
                                    item.Flagged = true;
                                    return false;
                                }
                            });
                            localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));
                        }
                    }
                }
                if(id == 4){
                    var currentText = $("#cdr-comment-"+ cdrId).text();
                    $("#cdr-comment-"+ cdrId).empty();

                    var textboxObj = $("<input maxlength=500 type=text>").appendTo("#cdr-comment-"+ cdrId);
                    textboxObj.on("focus", function(){
                        HidePopup(500);
                    });
                    textboxObj.on("blur", function(){
                        var newText = $(this).val();
                        SaveComment(cdrId, buddy, newText);
                    });
                    textboxObj.keypress(function(event){
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if (keycode == '13') {
                            event.preventDefault();

                            var newText = $(this).val();
                            SaveComment(cdrId, buddy, newText);
                        }
                    });
                    textboxObj.val(currentText);
                    textboxObj.focus();
                }

                // Text Messages
                if(id == 10){
                    var msgtext = $("#msg-text-"+ cdrId).text();
                    navigator.clipboard.writeText(msgtext).then(function(){
                        console.log("Text copied to the clipboard:", msgtext);
                    }).catch(function(){
                        console.error("Error writing to the clipboard:", e);
                    });
                }
                if(id == 11){
                    // TODO...
                    // Involves sharing a message ID, then on change, sent update request
                    // So that both parties share the same update.
                }
                if(id == 12){
                    var msgtext = $("#msg-text-"+ cdrId).text();
                    msgtext = "\""+ msgtext + "\"";
                    var textarea = $("#contact-"+ buddy +"-ChatMessage");
                    console.log("Quote Message:", msgtext);
                    textarea.val(msgtext +"\n" + textarea.val());
                }

                // Delete CDR
                // TODO: This doesn't look for the cdr or the QOS, don't use this
                if(id == 20){
                    var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
                    if(currentStream != null || currentStream.DataCollection != null){
                        $.each(currentStream.DataCollection, function (i, item) {
                            if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                                // Found
                                currentStream.DataCollection.splice(i, 1);
                                return false;
                            }
                        });
                        localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));
                        RefreshStream(FindBuddyByIdentity(buddy));
                    }
                }
                // Delete Poster Image
                if(id == 21){
                    var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
                    if(currentStream != null || currentStream.DataCollection != null){
                        $.each(currentStream.DataCollection, function (i, item) {
                            if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                                // Found
                                if(item.Recordings && item.Recordings.length >= 1){
                                    $.each(item.Recordings, function(r, recording) {
                                        recording.Poster = null;
                                    });
                                }
                                console.log("Poster Image Deleted");
                                return false;
                            }
                        });
                        localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));
                        RefreshStream(FindBuddyByIdentity(buddy));
                    }
                }

            }
        },
        createEvent : null,
        autoFocus : true,
        items : items
    }
    PopupMenu(obj, menu);
}
function SaveComment(cdrId, buddy, newText){
    console.log("Setting Comment:", newText);

    $("#cdr-comment-"+ cdrId).empty();
    $("#cdr-comment-"+ cdrId).append(newText);

    // Update DB
    var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
    if(currentStream != null || currentStream.DataCollection != null){
        $.each(currentStream.DataCollection, function (i, item) {
            if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                // Found
                item.MessageData = newText;
                return false;
            }
        });
        localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));
    }
}
function TagKeyPress(event, obj, cdrId, buddy){
    HidePopup(500);

    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13' || keycode == '44') {
        event.preventDefault();

        if ($(obj).val() == "") return;

        console.log("Adding Tag:", $(obj).val());

        $("#cdr-tags-"+ cdrId+" li:last").before("<li onclick=\"TagClick(this, '"+ cdrId +"', '"+ buddy +"')\">"+ $(obj).val() +"</li>");
        $(obj).val("");

        // Update DB
        UpdateTags(cdrId, buddy);
    }
}
function TagClick(obj, cdrId, buddy){
    console.log("Removing Tag:", $(obj).text());
    $(obj).remove();

    // Update DB
    UpdateTags(cdrId, buddy);
}
function UpdateTags(cdrId, buddy){
    var currentStream = JSON.parse(localDB.getItem(buddy + "-stream"));
    if(currentStream != null || currentStream.DataCollection != null){
        $.each(currentStream.DataCollection, function (i, item) {
            if (item.ItemType == "CDR" && item.CdrId == cdrId) {
                // Found
                item.Tags = [];
                $("#cdr-tags-"+ cdrId).children('li').each(function () {
                    if($(this).prop("class") != "tagText") item.Tags.push({ value: $(this).text() });
                });
                return false;
            }
        });
        localDB.setItem(buddy + "-stream", JSON.stringify(currentStream));
    }
}

function TagFocus(obj){
    HidePopup(500);
}
function AddMenu(obj, buddy){
    if(UiCustomMessageAction){
        if(typeof web_hook_on_message_action !== 'undefined') {
            web_hook_on_message_action(buddy, obj);
        }
        return;
    }

    var items = [];
    if(EnableTextExpressions) items.push({ value: 1, icon : "fa fa-smile-o", text: lang.select_expression });
    if(EnableTextDictate) items.push({ value: 2, icon : "fa fa-microphone", text: lang.dictate_message });
    // TODO
    if(EnableSendFiles) menu.push({ value: 3, name: "<i class=\"fa fa-share-alt\"></i> Share File" });
    if(EnableSendImages) menu.push({ value: 4, name: "<i class=\"fa fa-camera\"></i> Take/Share Picture" });
    if(EnableAudioRecording) menu.push({ value: 5, name: "<i class=\"fa fa-file-audio-o\"></i> Record Audio Message" });
    if(EnableVideoRecording) menu.push({ value: 6, name: "<i class=\"fa fa-file-video-o\"></i> Record Video Message" });
    // items.push();
    // items.push();
    // items.push();
    // items.push();
    // items.push();

    var menu = {
        selectEvent : function( event, ui ) {
            var id = ui.item.attr("value");
            HidePopup();
            if(id != null) {
                // Emoji Bar
                if(id == "1"){
                    ShowEmojiBar(buddy);
                }
                // Dictate Message
                if(id == "2"){
                    ShowDictate(buddy);
                }
                //
            }
        },
        createEvent : null,
        autoFocus : true,
        items : items
    }
    PopupMenu(obj, menu);
}
function ShowEmojiBar(buddy){
    var messageContainer = $("#contact-"+ buddy +"-emoji-menu");
    var textarea = $("#contact-"+ buddy +"-ChatMessage");

    var menuBar = $("<div/>");
    menuBar.prop("class", "emojiButton")
    var emojis = ["😀","😁","😂","😃","😄","😅","😆","😇","😈","😉","😊","😋","😌","😍","😎","😏","😐","😑","😒","😓","😔","😕","😖","😗","😘","😙","😚","😛","😜","😝","😞","😟","😠","😡","😢","😣","😤","😥","😦","😧","😨","😩","😪","😫","😬","😭","😮","😯","😰","😱","😲","😳","😴","😵","😶","😷","🙁","🙂","🙃","🙄","🤐","🤑","🤒","🤓","🤔","🤕","🤠","🤡","🤢","🤣","🤤","🤥","🤧","🤨","🤩","🤪","🤫","🤬","🤭","🤮","🤯","🧐"];
    $.each(emojis, function(i,e){
        var emoji = $("<button>");
        emoji.html(e);
        emoji.on('click', function(){
            var i = textarea.prop('selectionStart');
            var v = textarea.val();
            textarea.val(v.substring(0, i) + $(this).html() + v.substring(i, v.length));
            messageContainer.hide();

            updateScroll(buddy);
        });
        menuBar.append(emoji);
    });

    messageContainer.empty();
    messageContainer.append(menuBar);
    messageContainer.show();

    updateScroll(buddy);
}
function ShowDictate(buddy){
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null){
        return;
    }

    if(buddyObj.recognition != null){
        buddyObj.recognition.abort();
        buddyObj.recognition = null;
    }
    try {
        // Limitation: This object can only be made once on the page
        // Generally this is fine, as you can only really dictate one message at a time.
        // It will use the most recently created object.
        var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        buddyObj.recognition = new SpeechRecognition();
    }
    catch(e) {
        console.error(e);
        Alert(lang.alert_speech_recognition, lang.speech_recognition);
        return;
    }

    var instructions = $("<div/>");
    var messageContainer = $("#contact-"+ buddy +"-dictate-message");
    var textarea = $("#contact-"+ buddy +"-ChatMessage");

    buddyObj.recognition.continuous = true;
    buddyObj.recognition.onstart = function() {
        instructions.html("<i class=\"fa fa-microphone\" style=\"font-size: 21px\"></i><i class=\"fa fa-cog fa-spin\" style=\"font-size:10px; vertical-align:text-bottom; margin-left:2px\"></i> "+ lang.im_listening);
        updateScroll(buddy);
    }
    buddyObj.recognition.onspeechend = function() {
        instructions.html(lang.msg_silence_detection);
        window.setTimeout(function(){
            messageContainer.hide();
            updateScroll(buddy);
        }, 1000);
    }
    buddyObj.recognition.onerror = function(event) {
        if(event.error == 'no-speech') {
            instructions.html(lang.msg_no_speech);
        }
        else {
            if(buddyObj.recognition){
                console.warn("SpeechRecognition Error: ", event);
                buddyObj.recognition.abort();
            }
            buddyObj.recognition = null;
        }
        window.setTimeout(function(){
            messageContainer.hide();
            updateScroll(buddy);
        }, 1000);
    }
    buddyObj.recognition.onresult = function(event) {
        var transcript = event.results[event.resultIndex][0].transcript;
        if((event.resultIndex == 1 && transcript == event.results[0][0].transcript) == false) {
            if($.trim(textarea.val()).endsWith(".") || $.trim(textarea.val()) == "") {
                if(transcript == "\r" || transcript == "\n" || transcript == "\r\n" || transcript == "\t"){
                    // WHITESPACE ONLY
                }
                else {
                    transcript = $.trim(transcript);
                    transcript = transcript.replace(/^./, " "+ transcript[0].toUpperCase());
                }
            }
            console.log("Dictate:", transcript);
            textarea.val(textarea.val() + transcript);
        }
    }

    messageContainer.empty();
    messageContainer.append(instructions);
    messageContainer.show();

    updateScroll(buddy);

    buddyObj.recognition.start();
}

// My Profile
// ==========
function ShowMyProfile(){
    CloseUpSettings();



     // SIP & XMPP vCard
     var profileVcard = getDbItem("profileVcard", null);
     if(profileVcard != null) profileVcard = JSON.parse(profileVcard);


            var chatEng = ($("#chat_type_sip").is(':checked'))? "SIMPLE" : "XMPP";

            if(EnableAccountSettings){
                if($("#Configure_Account_wssServer").val() == "") {
                    console.warn("Validation Failed");
                    return;
                }
                if($("#Configure_Account_WebSocketPort").val() == "") {
                    console.warn("Validation Failed");
                    return;
                }
                if($("#Configure_Account_profileName").val() == "") {
                    console.warn("Validation Failed");
                    return;
                }
                if($("#Configure_Account_SipDomain").val() == "") {
                    console.warn("Validation Failed");
                    return;
                }
                if($("#Configure_Account_SipUsername").val() == "") {
                    console.warn("Validation Failed");
                    return;
                }
                if($("#Configure_Account_SipPassword").val() == "") {
                    console.warn("Validation Failed");
                    return;
                }
                if(chatEng == "XMPP"){
                    if($("#Configure_Account_xmpp_address").val() == "") {
                        console.warn("Validation Failed");
                        return;
                    }
                    if($("#Configure_Account_xmpp_port").val() == "") {
                        console.warn("Validation Failed");
                        return;
                    }
                    if($("#Configure_Account_xmpp_domain").val() == "") {
                        console.warn("Validation Failed");
                        return;
                    }
                    if($("#Configure_Account_profileUser").val() == "") {
                        console.warn("Validation Failed");
                        return;
                    }
                }
            }

            // The profileUserID identifies users
            if(localDB.getItem("profileUserID") == null) localDB.setItem("profileUserID", uID()); // For first time only

            // 1 Account
            if(EnableAccountSettings){

                localDB.setItem("wssServer", "youmats.andeverywhere.net");
                localDB.setItem("WebSocketPort", "8089");
                localDB.setItem("ServerPath", "/ws");
                localDB.setItem("profileName", "2001");
                localDB.setItem("SipDomain", "youmats.andeverywhere.net");
                localDB.setItem("SipUsername", "2001");
                localDB.setItem("SipPassword", "b03dbe258ea7eeb");
                localDB.setItem("VoiceMailSubscribe", false);
                localDB.setItem("VoicemailDid", "");

                localDB.setItem("ChatEngine", chatEng);

                localDB.setItem("XmppServer", $("#Configure_Account_xmpp_address").val());
                localDB.setItem("XmppWebsocketPort", $("#Configure_Account_xmpp_port").val());
                localDB.setItem("XmppWebsocketPath", $("#Configure_Account_xmpp_path").val());
                localDB.setItem("XmppDomain", $("#Configure_Account_xmpp_domain").val());
                localDB.setItem("profileUser", $("#Configure_Account_profileUser").val());
            }

            // 2 Audio & Video
            localDB.setItem("AutoGainControl", "1");
            localDB.setItem("EchoCancellation", "1");
            localDB.setItem("NoiseSuppression", "1");



            // 4 Notifications
            if(EnableNotificationSettings){
                localDB.setItem("Notifications", "0");
            }


    // Show
    $("#actionArea").show();

    // DoOnload
    window.setTimeout(function(){
        // Account
        if(EnableAccountSettings){
            $("#chat_type_sip").change(function(){
                if($("#chat_type_sip").is(':checked')){
                    $("#RowChatEngine_xmpp").hide();
                }
            });
            $("#chat_type_xmpp").change(function(){
                if($("#chat_type_xmpp").is(':checked')){
                    $("#RowChatEngine_xmpp").show();
                }
            });
            $("#Configure_Account_Voicemail_Subscribe").change(function(){
                if($("#Configure_Account_Voicemail_Subscribe").is(':checked')){
                    $("#Voicemail_Did_row").show();
                } else {
                    $("#Voicemail_Did_row").hide();
                }
            });
        }


        var playRingButton = $("#preview_ringer_play");
        // Ringtone Button Press
        playRingButton.click(function(){

            try{
                window.SettingsRingerAudio.pause();
            }
            catch(e){}
            window.SettingsRingerAudio = null;

            try{
                var tracks = window.SettingsRingerStream.getTracks();
                tracks.forEach(function(track) {
                    track.stop();
                });
            }
            catch(e){}
            window.SettingsRingerStream = null;

            try{
                var soundMeter = window.SettingsRingerStreamMeter;
                soundMeter.stop();
            }
            catch(e){}
            window.SettingsRingerStreamMeter = null;

            // Load Sample
            console.log("Audio:", audioBlobs.Ringtone.url);
            var audioObj = new Audio(audioBlobs.Ringtone.blob);
            audioObj.preload = "auto";
            audioObj.onplay = function(){
                var outputStream = new MediaStream();
                if (typeof audioObj.captureStream !== 'undefined') {
                    outputStream = audioObj.captureStream();
                }
                else if (typeof audioObj.mozCaptureStream !== 'undefined') {
                    return;
                    // BUG: mozCaptureStream() in Firefox does not work the same way as captureStream()
                    // the actual sound does not play out to the speakers... its as if the mozCaptureStream
                    // removes the stream from the <audio> object.
                    outputStream = audioObj.mozCaptureStream();
                }
                else if (typeof audioObj.webkitCaptureStream !== 'undefined') {
                    outputStream = audioObj.webkitCaptureStream();
                }
                else {
                    console.warn("Cannot display Audio Levels")
                    return;
                }
                // Monitor Output
                window.SettingsRingerStream = outputStream;
                window.SettingsRingerStreamMeter = MeterSettingsOutput(outputStream, "Settings_RingerOutput", "width", 50);
            }
            audioObj.oncanplaythrough = function(e) {
                if (typeof audioObj.sinkId !== 'undefined') {
                    audioObj.setSinkId(selectRingDevice.val()).then(function() {
                        console.log("Set sinkId to:", selectRingDevice.val());
                    }).catch(function(e){
                        console.warn("Failed not apply setSinkId.", e);
                    });
                }
                // Play
                audioObj.play().then(function(){
                    // Audio Is Playing
                }).catch(function(e){
                    console.warn("Unable to play audio file", e);
                });
                console.log("Playing sample audio file... ");
            }

            window.SettingsRingerAudio = audioObj;
        });

        // Audio Playback Source
        var selectAudioScr = $("#playbackSrc");
        // Handle output change (speaker)
        selectAudioScr.change(function(){
            console.log("Call to change Speaker ("+ this.value +")");

            var audioObj = window.SettingsOutputAudio;
            if(audioObj != null) {
                if (typeof audioObj.sinkId !== 'undefined') {
                    audioObj.setSinkId(this.value).then(function() {
                        console.log("sinkId applied to audioObj:", this.value);
                    }).catch(function(e){
                        console.warn("Failed not apply setSinkId.", e);
                    });
                }
            }
        });

        // Microphone
        var selectMicScr = $("#microphoneSrc");
        $("#Settings_AutoGainControl").prop("checked", AutoGainControl);
        $("#Settings_EchoCancellation").prop("checked", EchoCancellation);
        $("#Settings_NoiseSuppression").prop("checked", NoiseSuppression);
        // Handle Audio Source changes (Microphone)
        selectMicScr.change(function(){
            console.log("Call to change Microphone ("+ this.value +")");

            // Change and update visual preview
            try{
                var tracks = window.SettingsMicrophoneStream.getTracks();
                tracks.forEach(function(track) {
                    track.stop();
                });
                window.SettingsMicrophoneStream = null;
            }
            catch(e){}

            try{
                soundMeter = window.SettingsMicrophoneSoundMeter;
                soundMeter.stop();
                window.SettingsMicrophoneSoundMeter = null;
            }
            catch(e){}

            // Get Microphone
            var constraints = {
                audio: {
                    deviceId: { exact: this.value }
                },
                video: false
            }
            var localMicrophoneStream = new MediaStream();
            navigator.mediaDevices.getUserMedia(constraints).then(function(mediaStream){
                var audioTrack = mediaStream.getAudioTracks()[0];
                if(audioTrack != null){
                    // Display Micrphone Levels
                    localMicrophoneStream.addTrack(audioTrack);
                    window.SettingsMicrophoneStream = localMicrophoneStream;
                    window.SettingsMicrophoneSoundMeter = MeterSettingsOutput(localMicrophoneStream, "Settings_MicrophoneOutput", "width", 50);
                }
            }).catch(function(e){
                console.log("Failed to getUserMedia", e);
            });
        });



        if(navigator.mediaDevices){
            navigator.mediaDevices.enumerateDevices().then(function(deviceInfos){
                var savedVideoDevice = getVideoSrcID();
                var videoDeviceFound = false;

                var savedAudioDevice = getAudioSrcID();
                var audioDeviceFound = false;

                var MicrophoneFound = false;
                var SpeakerFound = false;
                var VideoFound = false;

                for (var i = 0; i < deviceInfos.length; ++i) {
                    console.log("Found Device ("+ deviceInfos[i].kind +"): ", deviceInfos[i].label);

                    // Check Devices
                    if (deviceInfos[i].kind === "audioinput") {
                        MicrophoneFound = true;
                        if(savedAudioDevice != "default" && deviceInfos[i].deviceId == savedAudioDevice) {
                            audioDeviceFound = true;
                        }
                    }
                    else if (deviceInfos[i].kind === "audiooutput") {
                        SpeakerFound = true;
                    }
                    else if (deviceInfos[i].kind === "videoinput") {
                        if(EnableVideoCalling == true){
                            VideoFound = true;
                            if(savedVideoDevice != "default" && deviceInfos[i].deviceId == savedVideoDevice) {
                                videoDeviceFound = true;
                            }
                        }
                    }
                }

                var contraints = {
                    audio: MicrophoneFound,
                    video: VideoFound
                }

                if(MicrophoneFound){
                    contraints.audio = { deviceId: "default" }
                    if(audioDeviceFound) contraints.audio.deviceId = { exact: savedAudioDevice }
                }

                if(EnableVideoCalling == true){
                    if(VideoFound){
                        contraints.video = { deviceId: "default" }
                        if(videoDeviceFound) contraints.video.deviceId = { exact: savedVideoDevice }
                    }
                    // Additional
                    if($("input[name=Settings_FrameRate]:checked").val() != ""){
                        contraints.video.frameRate = $("input[name=Settings_FrameRate]:checked").val();
                    }
                    if($("input[name=Settings_Quality]:checked").val() != ""){
                        contraints.video.height = $("input[name=Settings_Quality]:checked").val();
                    }
                    if($("input[name=Settings_AspectRatio]:checked").val() != ""){
                        contraints.video.aspectRatio = $("input[name=Settings_AspectRatio]:checked").val();
                    }
                }
                console.log("Get User Media", contraints);

                // Get User Media
                navigator.mediaDevices.getUserMedia(contraints).then(function(mediaStream){
                    // Note: This code may fire after the close button

                    // Handle Audio
                    settingsMicrophoneStreamTrack = (mediaStream.getAudioTracks().length >= 1)? mediaStream.getAudioTracks()[0] : null ;
                    if(MicrophoneFound && settingsMicrophoneStreamTrack != null){
                        settingsMicrophoneStream = new MediaStream();
                        settingsMicrophoneStream.addTrack(settingsMicrophoneStreamTrack);
                        // Display Micrphone Levels
                        // window.SettingsMicrophoneStream = settingsMicrophoneStream;
                        settingsMicrophoneSoundMeter = MeterSettingsOutput(settingsMicrophoneStream, "Settings_MicrophoneOutput", "width", 50);
                    }
                    else {
                        console.warn("No microphone devices found. Calling will not be possible.")
                    }

                    // Display Output Levels
                    $("#Settings_RingerOutput").css("width", "0%");
                    if(!SpeakerFound){
                        console.log("No speaker devices found, make sure one is plugged in.")
                        $("#playbackSrc").hide();
                        $("#RingDeviceSection").hide();
                    }

                    if(EnableVideoCalling == true){
                        // Handle Video
                        settingsVideoStreamTrack = (mediaStream.getVideoTracks().length >= 1)? mediaStream.getVideoTracks()[0] : null;
                        if(VideoFound && settingsVideoStreamTrack != null){
                            settingsVideoStream = new MediaStream();
                            settingsVideoStream.addTrack(settingsVideoStreamTrack);
                            // Display Preview Video
                            localVideo.srcObject = settingsVideoStream;
                            localVideo.onloadedmetadata = function(e) {
                                localVideo.play();
                            }
                        }
                        else {
                            console.warn("No video / webcam devices found. Video Calling will not be possible.")
                        }
                    }

                    // Return .then()
                    return navigator.mediaDevices.enumerateDevices();
                }).then(function(deviceInfos){
                    for (var i = 0; i < deviceInfos.length; ++i) {
                        console.log("Found Device ("+ deviceInfos[i].kind +") Again: ", deviceInfos[i].label, deviceInfos[i].deviceId);

                        var deviceInfo = deviceInfos[i];
                        var devideId = deviceInfo.deviceId;
                        var DisplayName = deviceInfo.label;
                        if(DisplayName.indexOf("(") > 0) DisplayName = DisplayName.substring(0,DisplayName.indexOf("("));

                        var option = $('<option/>');
                        option.prop("value", devideId);

                        if (deviceInfo.kind === "audioinput") {
                            option.text((DisplayName != "")? DisplayName : "Microphone");
                            if(getAudioSrcID() == devideId) option.prop("selected", true);
                            selectMicScr.append(option);
                        }
                        else if (deviceInfo.kind === "audiooutput") {
                            option.text((DisplayName != "")? DisplayName : "Speaker");
                            if(getAudioOutputID() == devideId) option.prop("selected", true);
                            selectAudioScr.append(option);
                            var ringOption = option.clone();
                            if(getRingerOutputID() == devideId) ringOption.prop("selected", true);
                            selectRingDevice.append(ringOption);
                        }
                        else if (deviceInfo.kind === "videoinput") {
                            if(EnableVideoCalling == true){
                                if(getVideoSrcID() == devideId) option.prop("selected", true);
                                option.text((DisplayName != "")? DisplayName : "Webcam");
                                selectVideoScr.append(option);
                            }
                        }
                    }
                    if(EnableVideoCalling == true){
                        // Add "Default" option
                        if(selectVideoScr.children('option').length > 0){
                            var option = $('<option/>');
                            option.prop("value", "default");
                            if(getVideoSrcID() == "default" || getVideoSrcID() == "" || getVideoSrcID() == "null") option.prop("selected", true);
                            option.text("("+ lang.default_video_src +")");
                            selectVideoScr.append(option);
                        }
                    }
                }).catch(function(e){
                    console.error(e);
                    Alert(lang.alert_error_user_media, lang.error);
                });
            }).catch(function(e){
                console.error("Error getting Media Devices", e);
            });
        }
        else {
            Alert(lang.alert_media_devices, lang.error);
        }

        // Appearance
        if(EnableAppearanceSettings){

            $("#Appearance_Html").show(); // Bit of an annoying bug... croppie has to be visible to work
            $("#ImageCanvas").croppie({
                viewport: { width: 150, height: 150, type: 'circle' }
            });

            // Preview Existing Image
            $("#ImageCanvas").croppie('bind', {
                url: getPicture("profilePicture")
            }).then(function(){
                $("#Appearance_Html").hide();
            });


            // Wireup File Change
            $("#fileUploader").change(function () {
                var filesArray = $(this).prop('files');

                if (filesArray.length == 1) {
                    var uploadId = Math.floor(Math.random() * 1000000000);
                    var fileObj = filesArray[0];
                    var fileName = fileObj.name;
                    var fileSize = fileObj.size;

                    if (fileSize <= 52428800) {
                        console.log("Adding (" + uploadId + "): " + fileName + " of size: " + fileSize + "bytes");

                        var reader = new FileReader();
                        reader.Name = fileName;
                        reader.UploadId = uploadId;
                        reader.Size = fileSize;
                        reader.onload = function (event) {
                            $("#ImageCanvas").croppie('bind', {
                                url: event.target.result
                            });
                        }

                        // Use onload for this
                        reader.readAsDataURL(fileObj);
                    }
                    else {
                        Alert(lang.alert_file_size, lang.error);
                    }
                }
                else {
                    Alert(lang.alert_single_file, lang.error);
                }
            });
        }

        // Notifications
        if(EnableNotificationSettings){
            var NotificationsCheck = $("#Settings_Notifications");
            NotificationsCheck.prop("checked", NotificationsActive);
            NotificationsCheck.change(function(){
                if(this.checked){
                    if(Notification.permission != "granted"){
                        if(checkNotificationPromise()){
                            Notification.requestPermission().then(function(p){
                                console.log(p);
                                HandleNotifyPermission(p);
                            });
                        }
                        else {
                            Notification.requestPermission(function(p){
                                console.log(p);
                                HandleNotifyPermission(p)
                            });
                        }
                    }
                }
            });
        }


    }, 0);

    window.location.reload();

}
function RefreshRegistration(){
    Unregister();
    console.log("Unregister complete...");
    window.setTimeout(function(){
        console.log("Starting registration...");
        Register();
    }, 1000);
}
function ToggleHeading(obj, div){
    $("#"+ div).toggle();
}
function ToggleAutoAnswer(){
    if(AutoAnswerPolicy == "disabled"){
        AutoAnswerEnabled = false;
        console.warn("Policy AutoAnswer: Disabled");
        return;
    }
    AutoAnswerEnabled = (AutoAnswerEnabled == true)? false : true;
    if(AutoAnswerPolicy == "enabled") AutoAnswerEnabled = true;
    localDB.setItem("AutoAnswerEnabled", (AutoAnswerEnabled == true)? "1" : "0");
    console.log("AutoAnswer:", AutoAnswerEnabled);
}
function ToggleDoNoDisturb(){
    if(DoNotDisturbPolicy == "disabled"){
        DoNotDisturbEnabled = false;
        console.warn("Policy DoNotDisturb: Disabled");
        return;
    }
    if(DoNotDisturbPolicy == "enabled") {
        DoNotDisturbEnabled = true;
        console.warn("Policy DoNotDisturb: Enabled");
        return;
    }
    if(DoNotDisturbEnabled == true){
        // Disable DND

        DoNotDisturbEnabled = false
        localDB.setItem("DoNotDisturbEnabled", "0");
        $("#dereglink").attr("class", "dotOnline");
        $("#dndStatus").html("");
        // Web Hook
        if(typeof web_hook_disable_dnd !== 'undefined') {
            web_hook_disable_dnd();
        }
    } else {
        // Enable DND

        DoNotDisturbEnabled = true
        localDB.setItem("DoNotDisturbEnabled", "1");
        $("#dereglink").attr("class", "dotDoNotDisturb");
        $("#dndStatus").html("(DND)");

        // Web Hook
        if(typeof web_hook_enable_dnd !== 'undefined') {
            web_hook_enable_dnd();
        }
    }
    console.log("DoNotDisturb", DoNotDisturbEnabled);
}
function ToggleCallWaiting(){
    if(CallWaitingPolicy == "disabled"){
        CallWaitingEnabled = false;
        console.warn("Policy CallWaiting: Disabled");
        return;
    }
    CallWaitingEnabled = (CallWaitingEnabled == true)? false : true;
    if(CallWaitingPolicy == "enabled") CallWaitingPolicy = true;
    localDB.setItem("CallWaitingEnabled", (CallWaitingEnabled == true)? "1" : "0");
    console.log("CallWaiting", CallWaitingEnabled);
}
function ToggleRecordAllCalls(){
    if(CallRecordingPolicy == "disabled"){
        RecordAllCalls = false;
        console.warn("Policy CallRecording: Disabled");
        return;
    }
    RecordAllCalls = (RecordAllCalls == true)? false : true;
    if(CallRecordingPolicy == "enabled") RecordAllCalls = true;
    localDB.setItem("RecordAllCalls", (RecordAllCalls == true)? "1" : "0");
    console.log("RecordAllCalls", RecordAllCalls);
}

// Device and Settings
// ===================
function ChangeSettings(lineNum, obj){

    // Check if you are in a call
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null) {
        console.warn("SIP Session is NULL.");
        return;
    }
    var session = lineObj.SipSession;

    // Load Devices
    if(!navigator.mediaDevices) {
        console.warn("navigator.mediaDevices not possible.");
        return;
    }

    var items = [];

    // Microphones
    for (var i = 0; i < AudioinputDevices.length; ++i) {
        var deviceInfo = AudioinputDevices[i];
        var devideId = deviceInfo.deviceId;
        var DisplayName = (deviceInfo.label)? deviceInfo.label : "Microphone";
        if(DisplayName.indexOf("(") > 0) DisplayName = DisplayName.substring(0,DisplayName.indexOf("("));
        var disabled = (session.data.AudioSourceDevice == devideId);

        items.push({value: "input-"+ devideId, icon : "fa fa-microphone", text: DisplayName, isDisabled : disabled });
    }

    var menu = {
        selectEvent : function( event, ui ) {
            var id = ui.item.attr("value");
            if(id != null) {

                // Microphone Device Change
                if(id.indexOf("input-") > -1){
                    var newid = id.replace("input-", "");

                    console.log("Call to change Microphone: ", newid);

                    HidePopup();

                    // First Stop Recording the call
                    var mustRestartRecording = false;
                    if(session.data.mediaRecorder && session.data.mediaRecorder.state == "recording"){
                        StopRecording(lineNum, true);
                        mustRestartRecording = true;
                    }

                    // Stop Monitoring
                    if(lineObj.LocalSoundMeter) lineObj.LocalSoundMeter.stop();

                    // Save Setting
                    session.data.AudioSourceDevice = newid;

                    var constraints = {
                        audio: {
                            deviceId: (newid != "default")? { exact: newid } : "default"
                        },
                        video: false
                    }
                    navigator.mediaDevices.getUserMedia(constraints).then(function(newStream){
                        // Assume that since we are selecting from a dropdown, this is possible
                        var newMediaTrack = newStream.getAudioTracks()[0];
                        var pc = session.sessionDescriptionHandler.peerConnection;
                        pc.getSenders().forEach(function (RTCRtpSender) {
                            if(RTCRtpSender.track && RTCRtpSender.track.kind == "audio") {
                                console.log("Switching Audio Track : "+ RTCRtpSender.track.label + " to "+ newMediaTrack.label);
                                RTCRtpSender.track.stop(); // Must stop, or this mic will stay in use
                                RTCRtpSender.replaceTrack(newMediaTrack).then(function(){
                                    // Start Recording again
                                    if(mustRestartRecording) StartRecording(lineNum);
                                }).catch(function(e){
                                    console.error("Error replacing track: ", e);
                                });
                            }
                        });
                    }).catch(function(e){
                        console.error("Error on getUserMedia");
                    });
                }


            }
            else {
                HidePopup();
            }
        },
        createEvent : null,
        autoFocus : true,
        items : items
    }
    PopupMenu(obj, menu);
}

// Media Presentation
// ==================
function PresentCamera(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null.");
        return;
    }
    var session = lineObj.SipSession;

    $("#line-"+ lineNum +"-src-camera").prop("disabled", true);
    $("#line-"+ lineNum +"-src-canvas").prop("disabled", false);
    $("#line-"+ lineNum +"-src-desktop").prop("disabled", false);
    $("#line-"+ lineNum +"-src-video").prop("disabled", false);
    $("#line-"+ lineNum +"-src-blank").prop("disabled", false);

    $("#line-"+ lineNum + "-scratchpad-container").hide();
    RemoveScratchpad(lineNum);
    $("#line-"+ lineNum +"-sharevideo").hide();
    $("#line-"+ lineNum +"-sharevideo").get(0).pause();
    $("#line-"+ lineNum +"-sharevideo").get(0).removeAttribute('src');
    $("#line-"+ lineNum +"-sharevideo").get(0).load();
    window.clearInterval(session.data.videoResampleInterval);

    $("#line-"+ lineNum + "-localVideo").show();
    $("#line-"+ lineNum + "-remote-videos").show();
    RedrawStage(lineNum, true);
    // $("#line-"+ lineNum + "-remoteVideo").appendTo("#line-"+ lineNum + "-stage-container");

    switchVideoSource(lineNum, session.data.VideoSourceDevice);
}
function PresentScreen(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null.");
        return;
    }
    var session = lineObj.SipSession;

    $("#line-"+ lineNum +"-src-camera").prop("disabled", false);
    $("#line-"+ lineNum +"-src-canvas").prop("disabled", false);
    $("#line-"+ lineNum +"-src-desktop").prop("disabled", true);
    $("#line-"+ lineNum +"-src-video").prop("disabled", false);
    $("#line-"+ lineNum +"-src-blank").prop("disabled", false);

    $("#line-"+ lineNum + "-scratchpad-container").hide();
    RemoveScratchpad(lineNum);
    $("#line-"+ lineNum +"-sharevideo").hide();
    $("#line-"+ lineNum +"-sharevideo").get(0).pause();
    $("#line-"+ lineNum +"-sharevideo").get(0).removeAttribute('src');
    $("#line-"+ lineNum +"-sharevideo").get(0).load();
    window.clearInterval(session.data.videoResampleInterval);

    $("#line-"+ lineNum + "-localVideo").show();
    $("#line-"+ lineNum + "-remote-videos").show();
    // $("#line-"+ lineNum + "-remoteVideo").appendTo("#line-"+ lineNum + "-stage-container");

    ShareScreen(lineNum);
}
function PresentScratchpad(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null.");
        return;
    }
    var session = lineObj.SipSession;

    $("#line-"+ lineNum +"-src-camera").prop("disabled", false);
    $("#line-"+ lineNum +"-src-canvas").prop("disabled", true);
    $("#line-"+ lineNum +"-src-desktop").prop("disabled", false);
    $("#line-"+ lineNum +"-src-video").prop("disabled", false);
    $("#line-"+ lineNum +"-src-blank").prop("disabled", false);

    $("#line-"+ lineNum + "-scratchpad-container").hide();
    RemoveScratchpad(lineNum);
    $("#line-"+ lineNum +"-sharevideo").hide();
    $("#line-"+ lineNum +"-sharevideo").get(0).pause();
    $("#line-"+ lineNum +"-sharevideo").get(0).removeAttribute('src');
    $("#line-"+ lineNum +"-sharevideo").get(0).load();
    window.clearInterval(session.data.videoResampleInterval);

    $("#line-"+ lineNum + "-localVideo").show();
    $("#line-"+ lineNum + "-remote-videos").hide();
    // $("#line-"+ lineNum + "-remoteVideo").appendTo("#line-"+ lineNum + "-preview-container");

    SendCanvas(lineNum);
}
function PresentVideo(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null.");
        return;
    }
    var session = lineObj.SipSession;

    var html = "<div class=\"UiWindowField\"><input type=file  accept=\"video/*\" id=SelectVideoToSend></div>";
    OpenWindow(html, lang.select_video, 150, 360, false, false, null, null, lang.cancel, function(){
        // Cancel
        CloseWindow();
    }, function(){
        // Do OnLoad
        $("#SelectVideoToSend").on('change', function(event){
            var input = event.target;
            if(input.files.length >= 1){
                CloseWindow();

                // Send Video (Can only send one file)
                SendVideo(lineNum, URL.createObjectURL(input.files[0]));
            }
            else {
                console.warn("Please Select a file to present.");
            }
        });
    }, null);
}
function PresentBlank(lineNum){
    var lineObj = FindLineByNumber(lineNum);
    if(lineObj == null || lineObj.SipSession == null){
        console.warn("Line or Session is Null.");
        return;
    }
    var session = lineObj.SipSession;

    $("#line-"+ lineNum +"-src-camera").prop("disabled", false);
    $("#line-"+ lineNum +"-src-canvas").prop("disabled", false);
    $("#line-"+ lineNum +"-src-desktop").prop("disabled", false);
    $("#line-"+ lineNum +"-src-video").prop("disabled", false);
    $("#line-"+ lineNum +"-src-blank").prop("disabled", true);

    $("#line-"+ lineNum + "-scratchpad-container").hide();
    RemoveScratchpad(lineNum);
    $("#line-"+ lineNum +"-sharevideo").hide();
    $("#line-"+ lineNum +"-sharevideo").get(0).pause();
    $("#line-"+ lineNum +"-sharevideo").get(0).removeAttribute('src');
    $("#line-"+ lineNum +"-sharevideo").get(0).load();
    window.clearInterval(session.data.videoResampleInterval);

    $("#line-"+ lineNum + "-localVideo").hide();
    $("#line-"+ lineNum + "-remote-videos").show();
    // $("#line-"+ lineNum + "-remoteVideo").appendTo("#line-"+ lineNum + "-stage-container");

    DisableVideoStream(lineNum);
}
function RemoveScratchpad(lineNum){
    var scratchpad = GetCanvas("line-" + lineNum + "-scratchpad");
    if(scratchpad != null){
        window.clearInterval(scratchpad.redrawIntrtval);

        RemoveCanvas("line-" + lineNum + "-scratchpad");
        $("#line-"+ lineNum + "-scratchpad-container").empty();

        scratchpad = null;
    }
}

// Chatting
// ========
function chatOnbeforepaste(event, obj, buddy){
    console.log("Handle paste, checking for Images...");
    var items = (event.clipboardData || event.originalEvent.clipboardData).items;

    // find pasted image among pasted items
    var preventDefault = false;
    for (var i = 0; i < items.length; i++) {
        if (items[i].type.indexOf("image") === 0) {
            console.log("Image found! Opening image editor...");

            var blob = items[i].getAsFile();

            // read the image in
            var reader = new FileReader();
            reader.onload = function (event) {

                // Image has loaded, open Image Preview editer
                // ===========================================
                console.log("Image loaded... setting placeholder...");
                var placeholderImage = new Image();
                placeholderImage.onload = function () {

                    console.log("Placeholder loaded... CreateImageEditor...");

                    CreateImageEditor(buddy, placeholderImage);
                }
                placeholderImage.src = event.target.result;

                // $("#contact-" + buddy + "-msgPreviewhtml").html("<img src=\""+ event.target.result +"\" style=\"max-width:320px; max-height:240px\" />");
                // $("#contact-" + buddy + "-msgPreview").show();
            }
            reader.readAsDataURL(blob);

            preventDefault = true;
            continue;
        }
    }

    // Pevent default if you found an image
    if (preventDefault) event.preventDefault();
}
function chatOnkeydown(event, obj, buddy) {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13'){
        if(event.shiftKey || event.ctrlKey) {
            // Leave as is
            // Windows and Mac react differently here.
        } else {
            event.preventDefault();

            SendChatMessage(buddy);
            return false;
        }
    } else{
        // Consult the chatstates
        var buddyObj = FindBuddyByIdentity(buddy);
        if(buddyObj != null && buddyObj.type == "xmpp") XmppStartComposing(buddyObj);
    }
}
function chatOnInput(event, obj, buddy) {
    var str = $.trim($(obj).val())
    if (str != "") {
        $("#contact-" + buddy + "-sendMessageButtons").show();
    }
    else {
        $("#contact-" + buddy + "-sendMessageButtons").hide();
    }
}


function ReformatMessage(str) {
    var msg = str;
    // Simple tex=>HTML
    msg = msg.replace(/</gi, "&lt;");
    msg = msg.replace(/>/gi, "&gt;");
    msg = msg.replace(/\n/gi, "<br>");
    // Emojy
    // Skype: :) :( :D :O ;) ;( (:| :| :P :$ :^) |-) |-( :x ]:)
    // (cool) (hearteyes) (stareyes) (like) (unamused) (cwl) (xd) (pensive) (weary) (hysterical) (flushed) (sweatgrinning) (disappointed) (loudlycrying) (shivering) (expressionless) (relieved) (inlove) (kiss) (yawn) (puke) (doh) (angry) (wasntme) (worry) (confused) (veryconfused) (mm) (nerd) (rainbowsmile) (devil) (angel) (envy) (makeup) (think) (rofl) (happy) (smirk) (nod) (shake) (waiting) (emo) (donttalk) (idea) (talk) (swear) (headbang) (learn) (headphones) (morningafter) (selfie) (shock) (ttm) (dream)
    msg = msg.replace(/(:\)|:\-\)|:o\))/g, String.fromCodePoint(0x1F642));     // :) :-) :o)
    msg = msg.replace(/(:\(|:\-\(|:o\()/g, String.fromCodePoint(0x1F641));     // :( :-( :o(
    msg = msg.replace(/(;\)|;\-\)|;o\))/g, String.fromCodePoint(0x1F609));     // ;) ;-) ;o)
    msg = msg.replace(/(:'\(|:'\-\()/g, String.fromCodePoint(0x1F62A));        // :'( :'‑(
    msg = msg.replace(/(:'\(|:'\-\()/g, String.fromCodePoint(0x1F602));        // :') :'‑)
    msg = msg.replace(/(:\$)/g, String.fromCodePoint(0x1F633));                // :$
    msg = msg.replace(/(>:\()/g, String.fromCodePoint(0x1F623));               // >:(
    msg = msg.replace(/(:\×)/g, String.fromCodePoint(0x1F618));                // :×
    msg = msg.replace(/(:\O|:\‑O)/g, String.fromCodePoint(0x1F632));             // :O :‑O
    msg = msg.replace(/(:P|:\-P|:p|:\-p)/g, String.fromCodePoint(0x1F61B));      // :P :-P :p :-p
    msg = msg.replace(/(;P|;\-P|;p|;\-p)/g, String.fromCodePoint(0x1F61C));      // ;P ;-P ;p ;-p
    msg = msg.replace(/(:D|:\-D)/g, String.fromCodePoint(0x1F60D));             // :D :-D

    msg = msg.replace(/(\(like\))/g, String.fromCodePoint(0x1F44D));           // (like)

    // Make clickable Hyperlinks
    msg = msg.replace(/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/gi, function (x) {
        var niceLink = (x.length > 50) ? x.substring(0, 47) + "..." : x;
        var rtn = "<A target=_blank class=previewHyperlink href=\"" + x + "\">" + niceLink + "</A>";
        return rtn;
    });
    return msg;
}
function getPicture(buddy, typestr, ignoreCache){
    var avatars = defaultAvatars.split(",");
    var rndInt = Math.floor(Math.random() * avatars.length);
    var defaultImg = hostingPrefix + "" + imagesDirectory + "" + avatars[rndInt].trim();
    if(buddy == "profilePicture"){
        // Special handling for profile image
        var dbImg = localDB.getItem("profilePicture");
        if(dbImg == null){
            return defaultImg;
        }
        else {
            return dbImg;
            // return URL.createObjectURL(base64toBlob(dbImg, 'image/png'));
        }
    }

    typestr = (typestr)? typestr : "extension";
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null){
        return defaultImg
    }
    if(ignoreCache != true && buddyObj.imageObjectURL != ""){
        // Use Cache
        return buddyObj.imageObjectURL;
    }
    var dbImg = localDB.getItem("img-"+ buddy +"-"+ typestr);
    if(dbImg == null){
        buddyObj.imageObjectURL = defaultImg
        return buddyObj.imageObjectURL
    }
    else {
        buddyObj.imageObjectURL = URL.createObjectURL(base64toBlob(dbImg, 'image/webp')); // image/png
        return buddyObj.imageObjectURL;
    }
}

// Image Editor
// ============
function CreateImageEditor(buddy, placeholderImage){
    // Show Interface
    // ==============
    console.log("Setting Up ImageEditor...");
    if($("#contact-" + buddy + "-imagePastePreview").is(":visible")) {
        console.log("Resetting ImageEditor...");
        $("#contact-" + buddy + "-imagePastePreview").empty();
        RemoveCanvas("contact-" + buddy + "-imageCanvas")
    } else {
        $("#contact-" + buddy + "-imagePastePreview").show();
    }
    // Create UI
    // =========

    var toolBarDiv = $('<div/>');
    toolBarDiv.css("margin-bottom", "5px")
    toolBarDiv.append('<button class="toolBarButtons" title="Select" onclick="ImageEditor_Select(\''+ buddy +'\')"><i class="fa fa-mouse-pointer"></i></button>');
    toolBarDiv.append('&nbsp;|&nbsp;');
    toolBarDiv.append('<button class="toolBarButtons" title="Draw" onclick="ImageEditor_FreedrawPen(\''+ buddy +'\')"><i class="fa fa-pencil"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Paint" onclick="ImageEditor_FreedrawPaint(\''+ buddy +'\')"><i class="fa fa-paint-brush"></i></button>');
    toolBarDiv.append('&nbsp;|&nbsp;');
    toolBarDiv.append('<button class="toolBarButtons" title="Select Line Color" onclick="ImageEditor_SetectLineColor(\''+ buddy +'\')"><i class="fa fa-pencil-square-o" style="color:rgb(255, 0, 0)"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Select Fill Color" onclick="ImageEditor_SetectFillColor(\''+ buddy +'\')"><i class="fa fa-pencil-square" style="color:rgb(255, 0, 0)"></i></button>');
    toolBarDiv.append('&nbsp;|&nbsp;');
    toolBarDiv.append('<button class="toolBarButtons" title="Add Circle" onclick="ImageEditor_AddCircle(\''+ buddy +'\')"><i class="fa fa-circle"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Add Rectangle" onclick="ImageEditor_AddRectangle(\''+ buddy +'\')"><i class="fa fa-stop"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Add Triangle" onclick="ImageEditor_AddTriangle(\''+ buddy +'\')"><i class="fa fa-play"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Add Emoji" onclick="ImageEditor_SetectEmoji(\''+ buddy +'\')"><i class="fa fa-smile-o"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Add Text" onclick="ImageEditor_AddText(\''+ buddy +'\')"><i class="fa fa-font"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Delete Selected Items" onclick="ImageEditor_Clear(\''+ buddy +'\')"><i class="fa fa-times"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Clear All" onclick="ImageEditor_ClearAll(\''+ buddy +'\')"><i class="fa fa-trash"></i></button>');
    toolBarDiv.append('&nbsp;|&nbsp;');
    toolBarDiv.append('<button class="toolBarButtons" title="Pan" onclick="ImageEditor_Pan(\''+ buddy +'\')"><i class="fa fa-hand-paper-o"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Zoom In" onclick="ImageEditor_ZoomIn(\''+ buddy +'\')"><i class="fa fa-search-plus"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Zoom Out" onclick="ImageEditor_ZoomOut(\''+ buddy +'\')"><i class="fa fa-search-minus"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Reset Pan & Zoom" onclick="ImageEditor_ResetZoom(\''+ buddy +'\')"><i class="fa fa-search" aria-hidden="true"></i></button>');
    toolBarDiv.append('&nbsp;|&nbsp;');
    toolBarDiv.append('<button class="toolBarButtons" title="Cancel" onclick="ImageEditor_Cancel(\''+ buddy +'\')"><i class="fa fa-times-circle"></i></button>');
    toolBarDiv.append('<button class="toolBarButtons" title="Send" onclick="ImageEditor_Send(\''+ buddy +'\')"><i class="fa fa-paper-plane"></i></button>');
    $("#contact-" + buddy + "-imagePastePreview").append(toolBarDiv);

    // Create the canvas
    // =================
    var newCanvas = $('<canvas/>');
    newCanvas.prop("id", "contact-" + buddy + "-imageCanvas");
    newCanvas.css("border", "1px solid #CCCCCC");
    $("#contact-" + buddy + "-imagePastePreview").append(newCanvas);
    console.log("Canvas for ImageEditor created...");

    var imgWidth = placeholderImage.width;
    var imgHeight = placeholderImage.height;
    var maxWidth = $("#contact-" + buddy + "-imagePastePreview").width()-2; // for the border
    var maxHeight = 480;
    $("#contact-" + buddy + "-imageCanvas").prop("width", maxWidth);
    $("#contact-" + buddy + "-imageCanvas").prop("height", maxHeight);

    // Handle Initial Zoom
    var zoomToFitImage = 1;
    var zoomWidth = 1;
    var zoomHeight = 1;
    if(imgWidth > maxWidth || imgHeight > maxHeight)
    {
        if(imgWidth > maxWidth)
        {
            zoomWidth = (maxWidth / imgWidth);
        }
        if(imgHeight > maxHeight)
        {
            zoomHeight = (maxHeight / imgHeight);
            console.log("Scale to fit height: "+ zoomHeight);
        }
        zoomToFitImage = Math.min(zoomWidth, zoomHeight) // need the smallest because less is more zoom.
        console.log("Scale down to fit: "+ zoomToFitImage);

        // Shape the canvas to fit the image and the new zoom
        imgWidth = imgWidth * zoomToFitImage;
        imgHeight = imgHeight * zoomToFitImage;
        console.log("resizing canvas to fit new image size...");
        $("#contact-" + buddy + "-imageCanvas").prop("width", imgWidth);
        $("#contact-" + buddy + "-imageCanvas").prop("height", imgHeight);
    }
    else {
        console.log("Image is able to fit, resizing canvas...");
        $("#contact-" + buddy + "-imageCanvas").prop("width", imgWidth);
        $("#contact-" + buddy + "-imageCanvas").prop("height", imgHeight);
    }

    // $("#contact-" + buddy + "-imageCanvas").css("cursor", "zoom-in");

    // Fabric Canvas API
    // =================
    console.log("Creating fabric API...");
    var canvas = new fabric.Canvas("contact-" + buddy + "-imageCanvas");
    canvas.id = "contact-" + buddy + "-imageCanvas";
    canvas.ToolSelected = "None";
    canvas.PenColour = "rgb(255, 0, 0)";
    canvas.PenWidth = 2;
    canvas.PaintColour = "rgba(227, 230, 3, 0.6)";
    canvas.PaintWidth = 10;
    canvas.FillColour = "rgb(255, 0, 0)";
    canvas.isDrawingMode = false;

    canvas.selectionColor = 'rgba(112,179,233,0.25)';
    canvas.selectionBorderColor = 'rgba(112,179,233, 0.8)';
    canvas.selectionLineWidth = 1;

    // canvas.setCursor('default');
    // canvas.rotationCursor = 'crosshair';
    // canvas.notAllowedCursor = 'not-allowed'
    // canvas.moveCursor = 'move';
    // canvas.hoverCursor = 'move';
    // canvas.freeDrawingCursor = 'crosshair';
    // canvas.defaultCursor = 'move';

    // canvas.selection = false; // Indicates whether group selection should be enabled
    // canvas.selectionKey = 'shiftKey' // Indicates which key or keys enable multiple click selection

    // Zoom to fit Width or Height
    // ===========================
    canvas.setZoom(zoomToFitImage);

    // Canvas Events
    // =============
    canvas.on('mouse:down', function(opt) {
        var evt = opt.e;

        if (this.ToolSelected == "Pan") {
            this.isDragging = true;
            this.selection = false;
            this.lastPosX = evt.clientX;
            this.lastPosY = evt.clientY;
        }
        // Make nicer grab handles
        if(opt.target != null){
            if(evt.altKey === true)
            {
                opt.target.lockMovementX = true;
            }
            if(evt.shiftKey === true)
            {
                opt.target.lockMovementY = true;
            }
            opt.target.set({
                transparentCorners: false,
                borderColor: 'rgba(112,179,233, 0.4)',
                cornerColor: 'rgba(112,179,233, 0.8)',
                cornerSize: 6
            });
        }
    });
    canvas.on('mouse:move', function(opt) {
        if (this.isDragging) {
            var e = opt.e;
            this.viewportTransform[4] += e.clientX - this.lastPosX;
            this.viewportTransform[5] += e.clientY - this.lastPosY;
            this.requestRenderAll();
            this.lastPosX = e.clientX;
            this.lastPosY = e.clientY;
        }
    });
    canvas.on('mouse:up', function(opt) {
        this.isDragging = false;
        this.selection = true;
        if(opt.target != null){
            opt.target.lockMovementX = false;
            opt.target.lockMovementY = false;
        }
    });
    canvas.on('mouse:wheel', function(opt) {
        var delta = opt.e.deltaY;
        var pointer = canvas.getPointer(opt.e);
        var zoom = canvas.getZoom();
        zoom = zoom + delta/200;
        if (zoom > 10) zoom = 10;
        if (zoom < 0.1) zoom = 0.1;
        canvas.zoomToPoint({ x: opt.e.offsetX, y: opt.e.offsetY }, zoom);
        opt.e.preventDefault();
        opt.e.stopPropagation();
    });

    // Add Image
    // ==========
    canvas.backgroundImage = new fabric.Image(placeholderImage);

    CanvasCollection.push(canvas);

    // Add Key Press Events
    // ====================
    $("#contact-" + buddy + "-imagePastePreview").keydown(function(evt) {
        evt = evt || window.event;
        var key = evt.keyCode;
        console.log("Key press on Image Editor ("+ buddy +"): "+ key);

        // Delete Key
        if (key == 46) ImageEditor_Clear(buddy);
    });

    console.log("ImageEditor: "+ canvas.id +" created");

    ImageEditor_FreedrawPen(buddy);
}
function GetCanvas(canvasId){
    for(var c = 0; c < CanvasCollection.length; c++){
        try {
            if(CanvasCollection[c].id == canvasId) return CanvasCollection[c];
        } catch(e) {
            console.warn("CanvasCollection.id not available");
        }
    }
    return null;
}
function RemoveCanvas(canvasId){
    for(var c = 0; c < CanvasCollection.length; c++){
        try{
            if(CanvasCollection[c].id == canvasId) {
                console.log("Found Old Canvas, Disposing...");

                CanvasCollection[c].clear()
                CanvasCollection[c].dispose();

                CanvasCollection[c].id = "--deleted--";

                console.log("CanvasCollection.splice("+ c +", 1)");
                CanvasCollection.splice(c, 1);
                break;
            }
        }
        catch(e){ }
    }
    console.log("There are "+ CanvasCollection.length +" canvas now.");
}
var ImageEditor_Select = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null) {
        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;
        return true;
    }
    return false;
}
var ImageEditor_FreedrawPen = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null) {
        canvas.freeDrawingBrush.color = canvas.PenColour;
        canvas.freeDrawingBrush.width = canvas.PenWidth;
        canvas.ToolSelected = "Draw";
        canvas.isDrawingMode = true;
        console.log(canvas)
        return true;
    }
    return false;
}
var ImageEditor_FreedrawPaint = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null) {
        canvas.freeDrawingBrush.color = canvas.PaintColour;
        canvas.freeDrawingBrush.width = canvas.PaintWidth;
        canvas.ToolSelected = "Paint";
        canvas.isDrawingMode = true;
        return true;
    }
    return false;
}
var ImageEditor_Pan = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.ToolSelected = "Pan";
        canvas.isDrawingMode = false;
        return true;
    }
    return false;
}
var ImageEditor_ResetZoom = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.setZoom(1);
        canvas.setViewportTransform([1,0,0,1,0,0]);
        // canvas.viewportTransform[4] = 0;
        // canvas.viewportTransform[5] = 0;
        return true;
    }
    return false;
}
var ImageEditor_ZoomIn = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        var zoom = canvas.getZoom();
        zoom = zoom + 0.5;
        if (zoom > 10) zoom = 10;
        if (zoom < 0.1) zoom = 0.1;

        var point = new fabric.Point(canvas.getWidth() / 2, canvas.getHeight() / 2);
        var center = fabric.util.transformPoint(point, canvas.viewportTransform);

        canvas.zoomToPoint(point, zoom);

        return true;
    }
    return false;
}
var ImageEditor_ZoomOut = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        var zoom = canvas.getZoom();
        zoom = zoom - 0.5;
        if (zoom > 10) zoom = 10;
        if (zoom < 0.1) zoom = 0.1;

        var point = new fabric.Point(canvas.getWidth() / 2, canvas.getHeight() / 2);
        var center = fabric.util.transformPoint(point, canvas.viewportTransform);

        canvas.zoomToPoint(point, zoom);

        return true;
    }
    return false;
}
var ImageEditor_AddCircle = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;
        var circle = new fabric.Circle({
            radius: 20, fill: canvas.FillColour
        })
        canvas.add(circle);
        canvas.centerObject(circle);
        canvas.setActiveObject(circle);
        return true;
    }
    return false;
}
var ImageEditor_AddRectangle = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;
        var rectangle = new fabric.Rect({
            width: 40, height: 40, fill: canvas.FillColour
        })
        canvas.add(rectangle);
        canvas.centerObject(rectangle);
        canvas.setActiveObject(rectangle);
        return true;
    }
    return false;
}
var ImageEditor_AddTriangle = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;
        var triangle = new fabric.Triangle({
            width: 40, height: 40, fill: canvas.FillColour
        })
        canvas.add(triangle);
        canvas.centerObject(triangle);
        canvas.setActiveObject(triangle);
        return true;
    }
    return false;
}
var ImageEditor_AddEmoji = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;
        var text = new fabric.Text(String.fromCodePoint(0x1F642), { fontSize : 24 });
        canvas.add(text);
        canvas.centerObject(text);
        canvas.setActiveObject(text);
        return true;
    }
    return false;
}
var ImageEditor_AddText = function (buddy, textString){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;
        var text = new fabric.IText(textString, { fill: canvas.FillColour, fontFamily: 'arial', fontSize : 18 });
        canvas.add(text);
        canvas.centerObject(text);
        canvas.setActiveObject(text);
        return true;
    }
    return false;
}
var ImageEditor_Clear = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;

        var activeObjects = canvas.getActiveObjects();
        for (var i=0; i<activeObjects.length; i++){
            canvas.remove(activeObjects[i]);
        }
        canvas.discardActiveObject();

        return true;
    }
    return false;
}
var ImageEditor_ClearAll = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        var savedBgImage = canvas.backgroundImage;

        canvas.ToolSelected = "none";
        canvas.isDrawingMode = false;
        canvas.clear();

        canvas.backgroundImage = savedBgImage;
        return true;
    }
    return false;
}
var ImageEditor_Cancel = function (buddy){
    console.log("Removing ImageEditor...");

    $("#contact-" + buddy + "-imagePastePreview").empty();
    RemoveCanvas("contact-" + buddy + "-imageCanvas");
    $("#contact-" + buddy + "-imagePastePreview").hide();
}
var ImageEditor_Send = function (buddy){
    var canvas = GetCanvas("contact-" + buddy + "-imageCanvas");
    if(canvas != null)
    {
        var imgData = canvas.toDataURL({ format: 'webp' });  //png
        SendImageDataMessage(buddy, imgData);
        return true;
    }
    return false;
}

// Find something in the message stream
// ====================================
function FindSomething(buddy) {
    $("#contact-" + buddy + "-search").toggle();
    if($("#contact-" + buddy + "-search").is(":visible") == false){
        RefreshStream(FindBuddyByIdentity(buddy));
    }
    updateScroll(buddy);
}
function TogglePinned(buddy){
    var buddyObj = FindBuddyByIdentity(buddy);
    if(buddyObj == null) return;

    if(buddyObj.Pinned){
        // Disable
        console.log("Disable Pinned for", buddy);
        buddyObj.Pinned = false;
    }
    else {
        // Enalbe
        console.log("Enable Pinned for", buddy);
        buddyObj.Pinned = true;
    }

    // Take Out
    var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
    if(json != null) {
        $.each(json.DataCollection, function (i, item) {
            if(item.uID == buddy || item.cID == buddy || item.gID == buddy){
                item.Pinned = buddyObj.Pinned;
                return false;
            }
        });
        // Put Back
        localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));
    }

    // Update View
    UpdateBuddyList();
}

// FileShare an Upload
// ===================
var allowDradAndDrop = function() {
    var div = document.createElement('div');
    return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
}
function onFileDragDrop(e, buddy){
    // drop

    var filesArray = e.dataTransfer.files;
    console.log("You are about to upload " + filesArray.length + " file.");

    // Clear style
    $("#contact-"+ buddy +"-ChatHistory").css("outline", "none");

    for (var f = 0; f < filesArray.length; f++){
        var fileObj = filesArray[f];
        var reader = new FileReader();
        reader.onload = function (event) {
            // console.log(event.target.result);

            // Check if the file is under 50MB
            if(fileObj.size <= 52428800){
                // Add to Stream
                // =============
                SendFileDataMessage(buddy, event.target.result, fileObj.name, fileObj.size);
            }
            else{
                alert("The file '"+ fileObj.name +"' is bigger than 50MB, you cannot upload this file")
            }
        }
        console.log("Adding: "+ fileObj.name + " of size: "+ fileObj.size +"bytes");
        reader.readAsDataURL(fileObj);
    }

    // Prevent Default
    preventDefault(e);
}
function cancelDragDrop(e, buddy){
    // dragleave dragend
    $("#contact-"+ buddy +"-ChatHistory").css("outline", "none");
    preventDefault(e);
}
function setupDragDrop(e, buddy){
    // dragover dragenter
    $("#contact-"+ buddy +"-ChatHistory").css("outline", "2px dashed #184369");
    preventDefault(e);
}
function preventDefault(e){
    e.preventDefault();
    e.stopPropagation();
}

// UI Elements
// ===========
// jQuery UI
function OpenWindow(html, title, height, width, hideCloseButton, allowResize, button1_Text, button1_onClick, button2_Text, button2_onClick, DoOnLoad, OnClose) {
    console.log("Open Window: " + title);

    // Close any windows that may already be open
    if(windowObj != null){
        windowObj.dialog("close");
        windowObj = null;
    }

    // Create Window
    windowObj = $('<div></div>').html(html).dialog({
        autoOpen: false,
        title: title,
        modal: true,
        width: width,
        height: height,
        resizable: allowResize,
        classes: { "ui-dialog-content": "cleanScroller"},
        close: function(event, ui) {
            $(this).dialog("destroy");
            windowObj = null;
        }
    });
    var buttons = [];
    if(button1_Text && button1_onClick){
        buttons.push({
            text: button1_Text,
            click: function(){
                console.log("Button 1 ("+ button1_Text +") Clicked");
                button1_onClick();
            }
        });
    }
    if(button2_Text && button2_onClick){
        buttons.push({
            text: button2_Text,
            click: function(){
                console.log("Button 2 ("+ button2_Text +") Clicked");
                button2_onClick();
            }
        });
    }
    if(buttons.length >= 1) windowObj.dialog( "option", "buttons", buttons);

    if(OnClose) windowObj.on("dialogbeforeclose", function(event, ui) {
        return OnClose(this);
    });
    if(DoOnLoad) windowObj.on("dialogopen", function(event, ui) {
        DoOnLoad();
    });

    // Open the Window
    windowObj.dialog("open");

    if (hideCloseButton) windowObj.dialog({ dialogClass: 'no-close' });
    // Doubl Click to maximise
    $(".ui-dialog-titlebar").dblclick(function(){
        var windowWidth = $(window).outerWidth()
        var windowHeight = $(window).outerHeight();
        windowObj.parent().css('top', '0px'); // option
        windowObj.parent().css('left', '0px');
        windowObj.dialog("option", "height", windowHeight); // option
        windowObj.dialog("option", "width", windowWidth);
        UpdateUI();
    });

    // Call UpdateUI to perform all the nesesary UI updates.
    UpdateUI();
}
function CloseWindow(all) {
    console.log("Call to close any open window");

    if(windowObj != null){
        windowObj.dialog("close");
        windowObj = null;
    }
    if(all == true){
        if (confirmObj != null) {
            confirmObj.dialog("close");
            confirmObj = null;
        }
        if (promptObj != null) {
            promptObj.dialog("close");
            promptObj = null;
        }
        if (alertObj != null) {
            alertObj.dialog("close");
            alertObj = null;
        }
    }
}
function WindowProgressOn() {
    //
}
function WindowProgressOff() {
    //
}
function Alert(messageStr, TitleStr, onOk) {
    if (confirmObj != null) {
        confirmObj.dialog("close");
        confirmObj = null;
    }
    if (promptObj != null) {
        promptObj.dialog("close");
        promptObj = null;
    }
    if (alertObj != null) {
        console.error("Alert not null, while Alert called: " + TitleStr + ", saying:" + messageStr);
        return;
    }
    else {
        console.log("Alert called with Title: " + TitleStr + ", saying: " + messageStr);
    }

    var html = "<div class=NoSelect>";
    html += "<div class=UiText style=\"padding: 10px\" id=AllertMessageText>" + messageStr + "</div>";
    html += "</div>"

    alertObj = $('<div>').html(html).dialog({
        autoOpen: false,
        title: TitleStr,
        modal: true,
        width: 300,
        height: "auto",
        resizable: false,
        closeOnEscape : false,
        close: function(event, ui) {
            $(this).dialog("destroy");
            alertObj = null;
        }
    });

    var buttons = [];
    buttons.push({
        text: lang.ok,
        click: function(){
            console.log("Alert OK clicked");
            if (onOk) onOk();
            $(this).dialog("close");
            alertObj = null;
        }
    });
    alertObj.dialog( "option", "buttons", buttons);

    // Open the Window
    alertObj.dialog("open");

    alertObj.dialog({ dialogClass: 'no-close' });

     // Call UpdateUI to perform all the nesesary UI updates.
     UpdateUI();

}
function Confirm(messageStr, TitleStr, onOk, onCancel) {
    if (alertObj != null) {
        alertObj.dialog("close");
        alertObj = null;
    }
    if (promptObj != null) {
        promptObj.dialog("close");
        promptObj = null;
    }
    if (confirmObj != null) {
        console.error("Confirm not null, while Confrim called with Title: " + TitleStr + ", saying: " + messageStr);
        return;
    }
    else {
        console.log("Confirm called with Title: " + TitleStr + ", saying: " + messageStr);
    }

    var html = "<div class=NoSelect>";
    html += "<div class=UiText style=\"padding: 10px\" id=ConfrimMessageText>" + messageStr + "</div>";
    html += "</div>";

    confirmObj = $('<div>').html(html).dialog({
        autoOpen: false,
        title: TitleStr,
        modal: true,
        width: 300,
        height: "auto",
        resizable: false,
        closeOnEscape : false,
        close: function(event, ui) {
            $(this).dialog("destroy");
            confirmObj = null;
        }
    });

    var buttons = [];
    buttons.push({
        text: lang.ok,
        click: function(){
            console.log("Confrim OK clicked");
            if (onOk) onOk();
            $(this).dialog("close");
            confirmObj = null;
        }
    });
    buttons.push({
        text: lang.cancel,
        click: function(){
            console.log("Confirm Cancel clicked");
            if (onCancel) onCancel();
            $(this).dialog("close");
            confirmObj = null;
        }
    });

    confirmObj.dialog( "option", "buttons", buttons);

    // Open the Window
    confirmObj.dialog("open");

    confirmObj.dialog({ dialogClass: 'no-close' });

    // Call UpdateUI to perform all the nesesary UI updates.
    UpdateUI();
}
function Prompt(messageStr, TitleStr, FieldText, defaultValue, dataType, placeholderText, onOk, onCancel) {
    if (alertObj != null) {
        alertObj.dialog("close");
        alertObj = null;
    }
    if (confirmObj != null) {
        confirmObj.dialog("close");
        confirmObj = null;
    }
    if (promptObj != null) {
        console.error("Prompt not null, while Prompt called with Title: " + TitleStr + ", saying: " + messageStr);
        return;
    }
    else {
        console.log("Prompt called with Title: " + TitleStr + ", saying: " + messageStr);
    }

    var html = "<div class=NoSelect>";
    html += "<div class=UiText style=\"padding: 10px\" id=PromptMessageText>";
    html += messageStr;
    html += "<div style=\"margin-top:10px\">" + FieldText + " : </div>";
    html += "<div style=\"margin-top:5px\"><INPUT id=PromptValueField type=" + dataType + " value=\"" + defaultValue + "\" placeholder=\"" + placeholderText + "\" style=\"width:98%\"></div>"
    html += "</div>";
    html += "</div>";

    promptObj = $('<div>').html(html).dialog({
        autoOpen: false,
        title: TitleStr,
        modal: true,
        width: 300,
        height: "auto",
        resizable: false,
        closeOnEscape : false,
        close: function(event, ui) {
            $(this).dialog("destroy");
            promptObj = null;
        }
    });

    var buttons = [];
    buttons.push({
        text: lang.ok,
        click: function(){
            console.log("Prompt OK clicked, with value: " + $("#PromptValueField").val());
            if (onOk) onOk($("#PromptValueField").val());
            $(this).dialog("close");
            promptObj = null;
        }
    });
    buttons.push({
        text: lang.cancel,
        click: function(){
            console.log("Prompt Cancel clicked");
            if (onCancel) onCancel();
            $(this).dialog("close");
            promptObj = null;
        }
    });
    promptObj.dialog( "option", "buttons", buttons);

    // Open the Window
    promptObj.dialog("open");

    promptObj.dialog({ dialogClass: 'no-close' });

    // Call UpdateUI to perform all the nesesary UI updates.
    UpdateUI();
}
function PopupMenu(obj, menu){
    console.log("Show Popup Menu");

    // Close any menu that may already be open
    if(menuObj != null){
        menuObj.menu("destroy");
        menuObj.empty();
        menuObj.remove();
        menuObj = null;
    }

    var x = $(obj).offset().left - $(document).scrollLeft();
    var y = $(obj).offset().top - $(document).scrollTop();
    var w = $(obj).outerWidth()
    var h = $(obj).outerHeight()

    menuObj = $("<ul></ul>");
    if(menu && menu.items){
        $.each(menu.items, function(i, item){
            var header = (item.isHeader == true)? " class=\"ui-widget-header\"" : "";
            var disabled = (item.isDisabled == true)? " class=\"ui-state-disabled\"" : "";
            if(item.icon != null){
                menuObj.append("<li value=\""+ item.value +"\" "+ header +" "+ disabled +"><div><span class=\""+ item.icon +" ui-icon\"></span>"+ item.text +"</div></li>");
            }
            else {
                menuObj.append("<li value=\""+ item.value +"\" "+ header +" "+ disabled +"><div>"+ item.text +"</div></li>");
            }
        });
    }

    // Attach UL to body
    menuObj.appendTo(document.body);

    // Create Menu
    menuObj.menu({});

    // Event wireup
    if(menu && menu.selectEvent){
        menuObj.on("menuselect", menu.selectEvent);
    }
    if(menu && menu.createEvent){
        menuObj.on("menucreate", menu.createEvent);
    }
    menuObj.on('blur',function(){
        HidePopup();
    });
    if(menu && menu.autoFocus == true) menuObj.focus();

    // Final Positions
    var menuWidth = menuObj.outerWidth()
    var left = x-((menuWidth/2)-(w/2));
    if(left + menuWidth + 10 > window.innerWidth){
        left = window.innerWidth - menuWidth;
    }
    if(left < 0) left = 0;
    menuObj.css("left",  left + "px");

    var menuHeight = menuObj.outerHeight()
    var top = y+h;
    if(top + menuHeight + 10 > window.innerHeight){
        top = window.innerHeight - menuHeight - 50;
    }
    if(top < 0) top = 0;
    menuObj.css("top", top + "px");

}

function HidePopup(timeout){
    if(timeout){
        window.setTimeout(function(){
            if(menuObj != null){
                menuObj.menu("destroy");
                try{
                    menuObj.empty();
                }
                catch(e){}
                try{
                    menuObj.remove();
                }
                catch(e){}
                menuObj = null;
            }
        }, timeout);
    } else {
        if(menuObj != null){
            menuObj.menu("destroy");
            try{
                menuObj.empty();
            }
            catch(e){}
            try{
                menuObj.remove();
            }
            catch(e){}
            menuObj = null;
        }
    }
}


// Device Detection
// ================
function DetectDevices(){
    navigator.mediaDevices.enumerateDevices().then(function(deviceInfos){
        // deviceInfos will not have a populated lable unless to accept the permission
        // during getUserMedia. This normally happens at startup/setup
        // so from then on these devices will be with lables.
        HasVideoDevice = false;
        HasAudioDevice = false;
        HasSpeakerDevice = false; // Safari and Firefox don't have these
        AudioinputDevices = [];
        VideoinputDevices = [];
        SpeakerDevices = [];
        for (var i = 0; i < deviceInfos.length; ++i) {
            if (deviceInfos[i].kind === "audioinput") {
                HasAudioDevice = true;
                AudioinputDevices.push(deviceInfos[i]);
            }
            else if (deviceInfos[i].kind === "audiooutput") {
                HasSpeakerDevice = true;
                SpeakerDevices.push(deviceInfos[i]);
            }
            else if (deviceInfos[i].kind === "videoinput") {
                if(EnableVideoCalling == true){
                    HasVideoDevice = true;
                    VideoinputDevices.push(deviceInfos[i]);
                }
            }
        }
        // console.log(AudioinputDevices, VideoinputDevices);
    }).catch(function(e){
        console.error("Error enumerating devices", e);
    });
}
DetectDevices();
window.setInterval(function(){
    DetectDevices();
}, 10000);

// =================================================================================

function onStatusChange(status) {
    // Strophe.ConnectionStatus = status;
    if (status == Strophe.Status.CONNECTING) {
        console.log('XMPP is connecting...');
    }
    else if (status == Strophe.Status.CONNFAIL) {
        console.warn('XMPP failed to connect.');
    }
    else if (status == Strophe.Status.DISCONNECTING) {
        console.log('XMPP is disconnecting.');
    }
    else if (status == Strophe.Status.DISCONNECTED) {
        console.log('XMPP is disconnected.');

        // Keep connected
        window.setTimeout(function(){
            // reconnectXmpp();
        }, 5 * 1000);
    }
    else if (status == Strophe.Status.CONNECTED) {
        console.log('XMPP is connected!');

        // Re-publish my vCard
        XmppSetMyVcard();

        // Get buddies
        XmppGetBuddies();

        XMPP.ping = window.setTimeout(function(){
            XmppSendPing();
        }, 45 * 1000);
    }
    else {
        console.log('XMPP is: ', Strophe.Status);
    }
}

function XmppSendPing(){
    // return;

    if(!XMPP || XMPP.connected == false) reconnectXmpp();

    var iq_request = $iq({"type":"get", "id":XMPP.getUniqueId(), "to":XmppDomain, "from":XMPP.jid});
    iq_request.c("ping", {"xmlns":"urn:xmpp:ping"});

    XMPP.sendIQ(iq_request, function (result){
        // console.log("XmppSendPing Response: ", result);
    }, function(e){
        console.warn("Error in Ping", e);
    }, 30 * 1000);

    XMPP.ping = window.setTimeout(function(){
        XmppSendPing();
    }, 45 * 1000);
    // TODO: Make this is a setting
}

// XMPP Presence
// =============
function XmppSetMyPresence(str, desc, updateVcard){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    // ["away", "chat", "dnd", "xa"] => ["Away", "Available", "Busy", "Gone"]

    console.log("Setting My Own Presence to: "+ str + "("+ desc +")");

    if(desc == "") desc = lang.default_status;
    $("#regStatus").html("<i class=\"fa fa-comments\"></i> "+ desc);

    var pres_request = $pres({"id": XMPP.getUniqueId(), "from": XMPP.jid });
    pres_request.c("show").t(str);
    if(desc && desc != ""){
        pres_request.root();
        pres_request.c("status").t(desc);
    }
    if(updateVcard == true){
        var base64 = getPicture("profilePicture");
        var imgBase64 = base64.split(",")[1];
        var photoHash = $.md5(imgBase64);

        pres_request.root();
        pres_request.c("x", {"xmlns": "vcard-temp:x:update"});
        if(photoHash){
            pres_request.c("photo", {}, photoHash);
        }
    }

    XMPP.sendPresence(pres_request, function (result){
        // console.log("XmppSetMyPresence Response: ", result);
    }, function(e){
        console.warn("Error in XmppSetMyPresence", e);
    }, 30 * 1000);
}
function onPresenceChange(presence) {
    // console.log('onPresenceChange', presence);

    var from = presence.getAttribute("from");
    var to = presence.getAttribute("to");

    var subscription = presence.getAttribute("subscription");
    var type = (presence.getAttribute("type"))? presence.getAttribute("type") : "presence"; // subscribe | subscribed | unavailable
    var pres = "";
    var status = "";
    var xmlns = "";
    Strophe.forEachChild(presence, "show", function(elem) {
        pres = elem.textContent;
    });
    Strophe.forEachChild(presence, "status", function(elem) {
        status = elem.textContent;
    });
    Strophe.forEachChild(presence, "x", function(elem) {
        xmlns = elem.getAttribute("xmlns");
    });

    var fromJid = Strophe.getBareJidFromJid(from);

    // Presence notification from me to me
    if(from == to){
        // Either my vCard updated, or my Presence updated
        return true;
    }

    // Find the buddy this message is coming from
    var buddyObj = FindBuddyByJid(fromJid);
    if(buddyObj == null) {

        // TODO: What to do here?

        console.warn("Buddy Not Found: ", fromJid);
        return true;
    }

    if(type == "subscribe"){
        // <presence xmlns="jabber:client" type="subscribe" from="58347g3721h~800@...com" id="1" subscription="both" to="58347g3721h~100@...com"/>
        // <presence xmlns="jabber:client" type="subscribe" from="58347g3721h~800@...com" id="1" subscription="both" to="58347g3721h~100@...com"/>

        // One of your buddies is requestion subscription
        console.log("Presence: "+ buddyObj.CallerIDName +" requesting subscrption");

        XmppConfirmSubscription(buddyObj);

        // Also Subscribe to them
        XmppSendSubscriptionRequest(buddyObj);

        UpdateBuddyList();
        return true;
    }
    if(type == "subscribed"){
        // One of your buddies has confimed subscription
        console.log("Presence: "+ buddyObj.CallerIDName +" confimed subscrption");

        UpdateBuddyList();
        return true;
    }
    if(type == "unavailable"){
        // <presence xmlns="jabber:client" type="unavailable" from="58347g3721h~800@...com/63zy33arw5" to="yas43lag8l@...com"/>
        console.log("Presence: "+ buddyObj.CallerIDName +" unavailable");

        UpdateBuddyList();
        return true;
    }

    if(xmlns == "vcard-temp:x:update"){
        // This is a presence update for the picture change
        console.log("Presence: "+ buddyObj.ExtNo +" - "+ buddyObj.CallerIDName +" vCard change");

        // Should check if the hash is different, could have been a non-picture change..
        // However, either way you would need to update the vCard, as there isnt a awy to just get the picture
        XmppGetBuddyVcard(buddyObj);

        UpdateBuddyList();
    }

    if(pres != "") {
        // This is a regulare
        console.log("Presence: "+ buddyObj.ExtNo +" - "+ buddyObj.CallerIDName +" is now: "+ pres +"("+ status +")");

        buddyObj.presence = pres;
        buddyObj.presenceText = (status == "")? lang.default_status : status;

        UpdateBuddyList();
    }

    return true;
}
function XmppConfirmSubscription(buddyObj){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var pres_request = $pres({"to": buddyObj.jid, "from": XMPP.jid, "type": "subscribed"});
    XMPP.sendPresence(pres_request);
    // Responses are handled in the main handler
}
function XmppSendSubscriptionRequest(buddyObj){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var pres_request = $pres({"to": buddyObj.jid, "from":XMPP.jid, "type": "subscribe" });
    XMPP.sendPresence(pres_request);
    // Responses are handled in the main handler
}

// XMPP Roster
// ===========
function XmppRemoveBuddyFromRoster(buddyObj){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var iq_request = $iq({"type":"set", "id":XMPP.getUniqueId(), "from":XMPP.jid});
    iq_request.c("query", {"xmlns": "jabber:iq:roster"});
    iq_request.c("item", {"jid": buddyObj.jid, "subscription":"remove"});
    if(buddyObj.jid == null){
        console.warn("Missing JID", buddyObj);
        return;
    }
    console.log("Removing "+ buddyObj.CallerIDName +"  from roster...")

    XMPP.sendIQ(iq_request, function (result){
        // console.log(result);
    });
}
function XmppAddBuddyToRoster(buddyObj){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var iq_request = $iq({"type":"set", "id":XMPP.getUniqueId(), "from":XMPP.jid});
    iq_request.c("query", {"xmlns": "jabber:iq:roster"});
    iq_request.c("item", {"jid": buddyObj.jid, "name": buddyObj.CallerIDName});
    if(buddyObj.jid == null){
        console.warn("Missing JID", buddyObj);
        return;
    }
    console.log("Adding "+ buddyObj.CallerIDName +"  to roster...")

    XMPP.sendIQ(iq_request, function (result){
        // console.log(result);
        XmppGetBuddyVcard(buddyObj);

        XmppSendSubscriptionRequest(buddyObj);
    });
}

function XmppGetBuddies(){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var iq_request = $iq({"type":"get", "id":XMPP.getUniqueId(), "from":XMPP.jid});
    iq_request.c("query", {"xmlns":"jabber:iq:roster"});
    console.log("Getting Buddy List (roster)...")

    XMPP.sendIQ(iq_request, function (result){
        // console.log("XmppGetBuddies Response: ", result);

        // Clear out only XMPP

        Strophe.forEachChild(result, "query", function(query) {
            Strophe.forEachChild(query, "item", function(buddyItem) {

                // console.log("Register Buddy", buddyItem);

                // <item xmlns="jabber:iq:roster" jid="58347g3721h~800@xmpp-eu-west-1.innovateasterisk.com" name="Alfredo Dixon" subscription="both"/>
                // <item xmlns="jabber:iq:roster" jid="58347g3721h~123456@conference.xmpp-eu-west-1.innovateasterisk.com" name="Some Group Name" subscription="both"/>

                var jid = buddyItem.getAttribute("jid");
                var displayName = buddyItem.getAttribute("name");
                var node = Strophe.getNodeFromJid(jid);
                var buddyDid = node;
                if(XmppRealm != "" && XmppRealmSeparator !="") {
                    buddyDid = node.split(XmppRealmSeparator,2)[1];
                }
                var ask = (buddyItem.getAttribute("ask"))? buddyItem.getAttribute("ask") : "none";
                var sub = (buddyItem.getAttribute("subscription"))? buddyItem.getAttribute("subscription") : "none";
                var isGroup = (jid.indexOf("@"+ XmppChatGroupService +".") > -1);

                var buddyObj = FindBuddyByJid(jid);
                if(buddyObj == null){
                    // Create Cache
                    if(isGroup == true){
                        console.log("Adding roster (group):", buddyDid, "-", displayName);
                        buddyObj = MakeBuddy("group", false, false, false, displayName, buddyDid, jid, false, buddyDid, false);
                    }
                    else {
                        console.log("Adding roster (xmpp):", buddyDid, "-", displayName);
                        buddyObj = MakeBuddy("xmpp", false, false, true, displayName, buddyDid, jid, false, buddyDid, false);
                    }

                    // RefreshBuddyData(buddyObj);
                    XmppGetBuddyVcard(buddyObj);
                }
                else {
                    // Buddy cache exists
                    console.log("Existing roster item:", buddyDid, "-", displayName);

                    // RefreshBuddyData(buddyObj);
                    XmppGetBuddyVcard(buddyObj);
                }

            });
        });

        // Update your own status, and get the status of others
        XmppSetMyPresence(getDbItem("XmppLastPresence", "chat"), getDbItem("XmppLastStatus", ""), true);

        // Populate the buddy list
        UpdateBuddyList();

    }, function(e){
        console.warn("Error Getting Roster", e);
    }, 30 * 1000);
}
function onBuddySetRequest(iq){
    console.log('onBuddySetRequest', iq);

    // <iq xmlns="jabber:client" type="result" id="4e9dadc7-145b-4ea2-ae82-3220130231ba" to="yas43lag8l@xmpp-eu-west-1.innovateasterisk.com/4gte25lhkh">
    //     <query xmlns="jabber:iq:roster" ver="1386244571">
    //          <item jid="800@xmpp-eu-west-1.innovateasterisk.com" name="Alfredo Dixon" subscription="both"/>
    //     </query>
    // </iq>

    return true;
}
function onBuddyUpdate(iq){

    return true;
}
function RefreshBuddyData(buddyObj){

    // Get vCard

    return;

    // Get Last Activity
    var iq_request = $iq({"type":"get", "id":XMPP.getUniqueId(), "to":buddyObj.jid, "from":XMPP.jid});
    iq_request.c("query", {"xmlns":"jabber:iq:last"});
    XMPP.sendIQ(iq_request, function (result){
        console.log("jabber:iq:last Response: ", result);

        if(result.children[0].getAttribute("seconds")){
            var seconds = Number(result.children[0].getAttribute("seconds"));
            lastActivity = moment().utc().subtract(seconds, 'seconds').format("YYYY-MM-DD HH:mm:ss UTC");

            UpdateBuddyActivity(buddyObj.identity, lastActivity);
        }

    }, function(e){
        console.warn("Error in jabber:iq:last", e);
    }, 30 * 1000);

}

// Profile (vCard)
// ===============
function XmppGetMyVcard(){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var iq_request = $iq({"type" : "get", "id" : XMPP.getUniqueId(), "from" : XMPP.jid});
    iq_request.c("vCard", {"xmlns" : "vcard-temp"});

    XMPP.sendIQ(iq_request, function (result){
        console.log("XmppGetMyVcard Response: ", result);



    }, function(e){
        console.warn("Error in XmppGetMyVcard", e);
    }, 30 * 1000);
}
function XmppSetMyVcard(){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var profileVcard = getDbItem("profileVcard", null);
    if(profileVcard == null || profileVcard == ""){
        console.warn("No vCard created yet");
        return;
    }
    profileVcard = JSON.parse(profileVcard);

    var base64 = getPicture("profilePicture");
    var imgBase64 = base64.split(",")[1];

    var iq_request = $iq({"type" : "set", "id" : XMPP.getUniqueId(), "from" : XMPP.jid});
    iq_request.c("vCard", {"xmlns" : "vcard-temp"});
    iq_request.c("FN", {}, profileName);
    iq_request.c("TITLE", {}, profileVcard.TitleDesc);
    iq_request.c("TEL");
    iq_request.c("NUMBER", {}, profileUser);
    iq_request.up();
    iq_request.c("TEL");
    iq_request.c("CELL", {}, profileVcard.Mobile);
    iq_request.up();
    iq_request.c("TEL");
    iq_request.c("VOICE", {}, profileVcard.Number1);
    iq_request.up();
    iq_request.c("TEL");
    iq_request.c("FAX", {}, profileVcard.Number2);
    iq_request.up();
    iq_request.c("EMAIL");
    iq_request.c("USERID", {}, profileVcard.Email);
    iq_request.up();
    iq_request.c("PHOTO");
    iq_request.c("TYPE", {}, "image/webp"); // image/png
    iq_request.c("BINVAL", {}, imgBase64);
    iq_request.up();
    iq_request.c("JABBERID", {}, Strophe.getBareJidFromJid(XMPP.jid));

    console.log("Sending vCard update");
    XMPP.sendIQ(iq_request, function (result){
        // console.log("XmppSetMyVcard Response: ", result);
    }, function(e){
        console.warn("Error in XmppSetMyVcard", e);
    }, 30 * 1000);
}
function XmppGetBuddyVcard(buddyObj){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    if(buddyObj == null) return;
    if(buddyObj.jid == null) return;

    var iq_request = $iq({"type" : "get", "id" : XMPP.getUniqueId(), "from" : XMPP.jid, "to": buddyObj.jid});
    iq_request.c("vCard", {"xmlns" : "vcard-temp"});
    XMPP.sendIQ(iq_request, function (result){

        var jid = result.getAttribute("from");
        console.log("Got vCard for: "+ jid);

        var buddyObj = FindBuddyByJid(jid);
        if(buddyObj == null) {
            console.warn("Received a vCard for non-existing buddy", jid)
            return;
        }

        var imgBase64 = "";

        Strophe.forEachChild(result, "vCard", function(vcard) {
            Strophe.forEachChild(vcard, null, function(element) {
                if(element.tagName == "FN"){
                    buddyObj.CallerIDName = element.textContent;
                }
                if(element.tagName == "TITLE"){
                    buddyObj.Desc = element.textContent;
                }
                if(element.tagName == "JABBERID"){
                    if(element.textContent != jid){
                        console.warn("JID does not match: ", element.textContent, jid);
                    }
                }
                if(element.tagName == "TEL"){
                    Strophe.forEachChild(element, "NUMBER", function(ExtNo) {
                        // Voip Number (Subscribe)
                        if(ExtNo.textContent != buddyObj.ExtNo){
                            console.warn("Subscribe Extension does not match: ", ExtNo.textContent, buddyObj.ExtNo);
                        }
                    });
                    Strophe.forEachChild(element, "CELL", function(cell) {
                        // Mobile
                        buddyObj.MobileNumber = cell.textContent;
                    });
                    Strophe.forEachChild(element, "VOICE", function(Alt1) {
                        // Alt1
                        buddyObj.ContactNumber1 = Alt1.textContent;
                    });
                    Strophe.forEachChild(element, "FAX", function(Alt2) {
                        // Alt2
                        buddyObj.ContactNumber2 = Alt2.textContent;
                    });
                }
                if(element.tagName == "EMAIL"){
                    Strophe.forEachChild(element, "USERID", function(email) {
                        buddyObj.Email = email.textContent;
                    });
                }
                if(element.tagName == "PHOTO"){
                    Strophe.forEachChild(element, "BINVAL", function(base64) {
                        imgBase64 = "data:image/webp;base64,"+ base64.textContent;  // data:image/png;base64,
                    });
                }
            });
        });

        // Save To DB
        var buddyJson = {};
        var itemId = -1;
        var json = JSON.parse(localDB.getItem(profileUserID + "-Buddies"));
        $.each(json.DataCollection, function (i, item) {
            if(item.uID == buddyObj.identity){
                buddyJson = item;
                itemId = i;
                return false;
            }
        });

        if(itemId != -1){

            buddyJson.MobileNumber = buddyObj.MobileNumber;
            buddyJson.ContactNumber1 = buddyObj.ContactNumber1;
            buddyJson.ContactNumber2 = buddyObj.ContactNumber2;
            buddyJson.DisplayName = buddyObj.CallerIDName;
            buddyJson.Description = buddyObj.Desc;
            buddyJson.Email = buddyObj.Email;

            json.DataCollection[itemId] = buddyJson;
            localDB.setItem(profileUserID + "-Buddies", JSON.stringify(json));
        }

        if(imgBase64 != ""){
            // console.log(buddyObj);
            console.log("Buddy: "+  buddyObj.CallerIDName + " picture updated");

            localDB.setItem("img-"+ buddyObj.identity + "-"+ buddyObj.type, imgBase64);
            $("#contact-"+ buddyObj.identity +"-picture-main").css("background-image", 'url('+ getPicture(buddyObj.identity, buddyObj.type, true) +')');
        }
        UpdateBuddyList();

    }, function(e){
        console.warn("Error in XmppGetBuddyVcard", e);
    }, 30 * 1000);
}

// XMPP Messaging
// ==============
function onMessage(message){
    // console.log('onMessage', message);

    var from = message.getAttribute("from");
    var fromJid = Strophe.getBareJidFromJid(from);
    var to = message.getAttribute("to");
    var messageId = message.getAttribute("id");

    // Determin Buddy
    var buddyObj = FindBuddyByJid(fromJid);
    if(buddyObj == null) {
        // You don't appear to be a buddy of mine

        // TODO: Handle this
        console.warn("Spam!"); // LOL :)
        return true;
    }

    var isDelayed = false;
    var DateTime = utcDateNow();
    Strophe.forEachChild(message, "delay", function(elem) {
        // Delay message received
        if(elem.getAttribute("xmlns") == "urn:xmpp:delay"){
            isDelayed = true;
            DateTime = moment(elem.getAttribute("stamp")).utc().format("YYYY-MM-DD HH:mm:ss UTC");
        }
    });
    var originalMessage = "";
    Strophe.forEachChild(message, "body", function(elem) {
        // For simplicity, this code is assumed to take the last body
        originalMessage = elem.textContent;
    });


    // chatstate
    var chatstate = "";
    Strophe.forEachChild(message, "composing", function(elem) {
        if(elem.getAttribute("xmlns") == "http://jabber.org/protocol/chatstates"){
            chatstate = "composing";
        }
    });
    Strophe.forEachChild(message, "paused", function(elem) {
        if(elem.getAttribute("xmlns") == "http://jabber.org/protocol/chatstates"){
            chatstate = "paused";
        }
    });
    Strophe.forEachChild(message, "active", function(elem) {
        if(elem.getAttribute("xmlns") == "http://jabber.org/protocol/chatstates"){
            chatstate = "active";
        }
    });
    if(chatstate == "composing"){
        if(!isDelayed) XmppShowComposing(buddyObj);
        return true;
    }
    else {
        XmppHideComposing(buddyObj);
    }

    // Message Correction
    var isCorrection = false;
    var targetCorrectionMsg = "";
    Strophe.forEachChild(message, "replace", function(elem) {
        if(elem.getAttribute("xmlns") == "urn:xmpp:message-correct:0"){
            isCorrection = true;
            Strophe.forEachChild(elem, "id", function(idElem) {
                targetCorrectionMsg = idElem.textContent;
            });
        }
    });
    if(isCorrection && targetCorrectionMsg != "") {
        console.log("Message "+ targetCorrectionMsg +" for "+ buddyObj.CallerIDName +" was corrected");
        CorrectMessage(buddyObj, targetCorrectionMsg, originalMessage);
    }

    // Delivery Events
    var eventStr = "";
    var targetDeliveryMsg = "";
    Strophe.forEachChild(message, "x", function(elem) {
        if(elem.getAttribute("xmlns") == "jabber:x:event"){
            // One of the delivery events occured
            Strophe.forEachChild(elem, "delivered", function(delElem) {
                eventStr = "delivered";
            });
            Strophe.forEachChild(elem, "displayed", function(delElem) {
                eventStr = "displayed";
            });
            Strophe.forEachChild(elem, "id", function(idElem) {
                targetDeliveryMsg = idElem.textContent;
            });
        }
    });
    if(eventStr == "delivered" && targetDeliveryMsg != "") {
        console.log("Message "+ targetDeliveryMsg +" for "+ buddyObj.CallerIDName +" was delivered");
        MarkDeliveryReceipt(buddyObj, targetDeliveryMsg, true);

        return true;
    }
    if(eventStr == "displayed" && targetDeliveryMsg != "") {
        console.log("Message "+ targetDeliveryMsg +" for "+ buddyObj.CallerIDName +" was displayed");
        MarkDisplayReceipt(buddyObj, targetDeliveryMsg, true);

        return true;
    }

    // Messages
    if(originalMessage == ""){
        // Not a full message
    }
    else {
        if(messageId) {
            // Although XMPP does not require message ID's, this application does
            XmppSendDeliveryReceipt(buddyObj, messageId);

            AddMessageToStream(buddyObj, messageId, "MSG", originalMessage, DateTime)
            UpdateBuddyActivity(buddyObj.identity);
            var streamVisible = $("#stream-"+ buddyObj.identity).is(":visible");
            if (streamVisible) {
                MarkMessageRead(buddyObj, messageId);
                XmppSendDisplayReceipt(buddyObj, messageId);
            }
            RefreshStream(buddyObj);
            ActivateStream(buddyObj, originalMessage);
        }
        else {
            console.warn("Sorry, messages must have an id ", message)
        }
    }

    return true;
}
function XmppShowComposing(buddyObj){
    console.log("Buddy is composing a message...");
    $("#contact-"+ buddyObj.identity +"-chatstate").show();
    $("#contact-"+ buddyObj.identity +"-presence").hide();
    $("#contact-"+ buddyObj.identity +"-presence-main").hide();
    $("#contact-"+ buddyObj.identity +"-chatstate-menu").show();
    $("#contact-"+ buddyObj.identity +"-chatstate-main").show();

    updateScroll(buddyObj.identity);
}
function XmppHideComposing(buddyObj){
    console.log("Buddy composing is done...");
    $("#contact-"+ buddyObj.identity +"-chatstate").hide();
    $("#contact-"+ buddyObj.identity +"-chatstate-menu").hide();
    $("#contact-"+ buddyObj.identity +"-chatstate-main").hide();
    $("#contact-"+ buddyObj.identity +"-presence").show();
    $("#contact-"+ buddyObj.identity +"-presence-main").show();

    updateScroll(buddyObj.identity);
}
function XmppSendMessage(buddyObj,message, messageId, thread, markable, type){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    if(!type) type = "normal"; // chat | error | normal | groupchat | headline
    var msg = $msg({"to": buddyObj.jid, "type": type, "id" : messageId, "from" : XMPP.jid})
    if(thread && thread != ""){
        msg.c("thread").t(thread);
        msg.up();
    }
    msg.c("body").t(message);
    // XHTML-IM
    msg.up();
    msg.c("active", {"xmlns": "http://jabber.org/protocol/chatstates"});
    msg.up();
    msg.c("x", {"xmlns": "jabber:x:event"});
    msg.c("delivered");
    msg.up();
    msg.c("displayed");

    console.log("sending message...");
    buddyObj.chatstate = "active";
    if(buddyObj.chatstateTimeout){
        window.clearTimeout(buddyObj.chatstateTimeout);
    }
    buddyObj.chatstateTimeout = null;

    try{
        XMPP.send(msg);
        MarkMessageSent(buddyObj, messageId, false);
    }
    catch(e){
        MarkMessageNotSent(buddyObj, messageId, false);
    }
}
function XmppStartComposing(buddyObj, thread){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    if(buddyObj.jid == null || buddyObj.jid == "") return;

    if(buddyObj.chatstateTimeout){
        window.clearTimeout(buddyObj.chatstateTimeout);
    }
    buddyObj.chatstateTimeout = window.setTimeout(function(){
        XmppPauseComposing(buddyObj, thread);
    }, 10 * 1000);

    if(buddyObj.chatstate && buddyObj.chatstate == "composing") return;

    var msg = $msg({"to": buddyObj.jid, "from" : XMPP.jid})
    if(thread && thread != ""){
        msg.c("thread").t(thread);
        msg.up();
    }
    msg.c("composing", {"xmlns": "http://jabber.org/protocol/chatstates"});

    console.log("you are composing a message...")
    buddyObj.chatstate = "composing";

    XMPP.send(msg);
}
function XmppPauseComposing(buddyObj, thread){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    if(buddyObj.jid == null || buddyObj.jid == "") return;

    if(buddyObj.chatstate && buddyObj.chatstate == "paused") return;

    var msg = $msg({"to": buddyObj.jid, "from" : XMPP.jid})
    if(thread && thread != ""){
        msg.c("thread").t(thread);
        msg.up();
    }
    msg.c("paused", {"xmlns": "http://jabber.org/protocol/chatstates"});

    console.log("You have paused your message...");
    buddyObj.chatstate = "paused";
    if(buddyObj.chatstateTimeout){
        window.clearTimeout(buddyObj.chatstateTimeout);
    }
    buddyObj.chatstateTimeout = null;

    XMPP.send(msg);
}
function XmppSendDeliveryReceipt(buddyObj, id){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var msg = $msg({"to": buddyObj.jid, "from" : XMPP.jid});
    msg.c("x", {"xmlns": "jabber:x:event"});
    msg.c("delivered");
    msg.up();
    msg.c("id").t(id);

    console.log("sending delivery notice for "+ id +"...");

    XMPP.send(msg);
}
function XmppSendDisplayReceipt(buddyObj, id){
    if(!XMPP || XMPP.connected == false) {
        console.warn("XMPP not connected");
        return;
    }

    var msg = $msg({"to": buddyObj.jid, "from" : XMPP.jid});
    msg.c("x", {"xmlns": "jabber:x:event"});
    msg.c("displayed");
    msg.up();
    msg.c("id").t(id);

    console.log("sending display notice for "+ id +"...");

    XMPP.send(msg);
}

// XMPP Other
// ==========
function onPingRequest(iq){
    // Handle Ping Pong
    // <iq type="get" id="86-14" from="localhost" to="websocketuser@localhost/cc9fd219" >
    //     <ping xmlns="urn:xmpp:ping"/>
    // </iq>
    var id = iq.getAttribute("id");
    var to = iq.getAttribute("to");
    var from = iq.getAttribute("from");

    var iq_response = $iq({'type':'result', 'id':id, 'to':from, 'from':to});
    XMPP.send(iq_response);

    return true;
}
function onVersionRequest(iq){
    // Handle Request for our version etc
    // <iq xmlns="jabber:client" type="get" id="419-24" to=".../..." from="innovateasterisk.com">
    //     <query xmlns="jabber:iq:version"/>
    // </iq>
    var id = iq.getAttribute("id");
    var to = iq.getAttribute("to");
    var from = iq.getAttribute("from");

    var iq_response = $iq({'type':'result', 'id':id, 'to':from, 'from':to});
    iq_response.c('query', {'xmlns':'jabber:iq:version'});
    iq_response.c('name', null, 'Browser Phone');
    iq_response.c('version', null, '0.0.1');
    iq_response.c('os', null, 'Browser');
    XMPP.send(iq_response);

    return true;
}


function onInfoQuery(iq){
    console.log('onInfoQuery', iq);

    // Probably a result
    return true;
}
function onInfoQueryRequest(iq){
    console.log('onInfoQueryRequest', iq);

    var query = ""; // xml.find("iq").find("query").attr("xmlns");
    Strophe.forEachChild(iq, "query", function(elem) {
        query = elem.getAttribute("xmlns");
    });
    console.log(query);

    // ??
    return true;
}
function onInfoQueryCommand(iq){
    console.log('onInfoQueryCommand', iq);

    var query = ""; // xml.find("iq").find("query").attr("xmlns");
    Strophe.forEachChild(iq, "query", function(elem) {
        query = elem.getAttribute("xmlns");
    });
    console.log(query);

    // ??
    return true;
}
function XMPP_GetGroups(){
    var iq_request = $iq({"type" : "get", "id" : XMPP.getUniqueId(), "to" : XmppChatGroupService +"."+ XmppDomain, "from" : XMPP.jid});
    iq_request.c("query", {"xmlns" : "http://jabber.org/protocol/disco#items", "node" : "http://jabber.org/protocol/muc#rooms"});

    XMPP.sendIQ(iq_request, function (result){
        console.log("GetGroups Response: ", result);
    }, function(e){
        console.warn("Error in GetGroups", e);
    }, 30 * 1000);
}
function XMPP_GetGroupMembers(){
    var iq_request = $iq({"type" : "get", "id" : XMPP.getUniqueId(), "to" : "directors@"+ XmppChatGroupService +"."+ XmppDomain, "from" : XMPP.jid});
    iq_request.c("query", {"xmlns":"http://jabber.org/protocol/disco#items"});

    XMPP.sendIQ(iq_request, function (result){
        console.log("GetGroupMembers Response: ", result);
    }, function(e){
        console.warn("Error in GetGroupMembers", e);
    }, 30 * 1000);
}
function XMPP_JoinGroup(){
    var pres_request = $pres({"id" : XMPP.getUniqueId(), "from" : XMPP.jid, "to" : "directors@"+ XmppChatGroupService +"."+ XmppDomain +"/nickname" });
    pres_request.c("x", {"xmlns" : "http://jabber.org/protocol/muc" });

    XMPP.sendPresence(pres_request, function (result){
        console.log("JoinGroup Response: ", result);
    }, function(e){
        console.warn("Error in Set Presence", e);
    }, 30 * 1000);
}
function XMPP_QueryMix(){
    var iq_request = $iq({"type" : "get", "id" : XMPP.getUniqueId(), "from" : XMPP.jid});
    iq_request.c("query", {"xmlns" : "http://jabber.org/protocol/disco#info"});

    XMPP.sendIQ(iq_request, function (result){
        console.log("XMPP_QueryMix Response: ", result);
    }, function(e){
        console.warn("Error in XMPP_QueryMix", e);
    }, 30 * 1000);
}

var XMPP = null;
var reconnectXmpp = function(){
    console.log("Connect/Reconnect XMPP connection...");

    if(XMPP) XMPP.disconnect("");
    if(XMPP) XMPP.reset();

    var xmpp_websocket_uri = "wss://"+ XmppServer +":"+ XmppWebsocketPort +""+ XmppWebsocketPath;
    var xmpp_username = profileUser +"@"+ XmppDomain;
    if(XmppRealm != "" && XmppRealmSeparator) xmpp_username = XmppRealm + XmppRealmSeparator + xmpp_username;
    var xmpp_password = SipPassword;

    XMPP = null;
    if(XmppDomain == "" || XmppServer == "" || XmppWebsocketPort == "" || XmppWebsocketPath == ""){
        console.log("Cannot connect to XMPP: ", XmppDomain, XmppServer, XmppWebsocketPort, XmppWebsocketPath);
        return;
    }
    XMPP = new Strophe.Connection(xmpp_websocket_uri);

    // XMPP.rawInput = function(data){
    //     console.log('RECV:', data);
    // }
    // XMPP.rawOutput = function(data){
    //     console.log('SENT:', data);
    // }

    // Information Query
    XMPP.addHandler(onPingRequest, "urn:xmpp:ping", "iq", "get");
    XMPP.addHandler(onVersionRequest, "jabber:iq:version", "iq", "get");

    // Presence
    XMPP.addHandler(onPresenceChange, null, "presence", null);
    // Message
    XMPP.addHandler(onMessage, null, "message", null);

    console.log("XMPP connect...");

    XMPP.connect(xmpp_username, xmpp_password, onStatusChange);
}
/*! For license information please see sip.min.js.LICENSE.txt */
!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.SIP=t():e.SIP=t()}(this,(function(){return(()=>{"use strict";var e={d:(t,s)=>{for(var i in s)e.o(s,i)&&!e.o(t,i)&&Object.defineProperty(t,i,{enumerable:!0,get:s[i]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};e.r(t),e.d(t,{Ack:()=>l,Bye:()=>g,ContentTypeUnsupportedError:()=>o,Core:()=>s,EmitterImpl:()=>u,Grammar:()=>y,Info:()=>p,Invitation:()=>z,Inviter:()=>X,Message:()=>G,Messager:()=>Q,NameAddrHeader:()=>m,Notification:()=>V,Parameters:()=>f,Publisher:()=>ce,PublisherState:()=>ee,Referral:()=>K,Registerer:()=>he,RegistererState:()=>te,RequestPendingError:()=>a,SIPExtension:()=>Y,Session:()=>Z,SessionDescriptionHandlerError:()=>c,SessionState:()=>W,SessionTerminatedError:()=>h,StateTransitionError:()=>d,Subscriber:()=>le,Subscription:()=>de,SubscriptionState:()=>ie,TransportState:()=>re,URI:()=>v,UserAgent:()=>ct,UserAgentRegisteredOptionTags:()=>J,UserAgentState:()=>ne,Web:()=>i,equivalentURI:()=>w,name:()=>St,version:()=>yt});var s={};e.r(s),e.d(s,{ByeUserAgentClient:()=>Ae,ByeUserAgentServer:()=>ke,C:()=>L,CancelUserAgentClient:()=>ht,ClientTransaction:()=>ye,Dialog:()=>$e,DigestAuthentication:()=>ve,Exception:()=>n,Grammar:()=>y,IncomingMessage:()=>A,IncomingRequestMessage:()=>D,IncomingResponseMessage:()=>H,InfoUserAgentClient:()=>_e,InfoUserAgentServer:()=>Pe,InviteClientTransaction:()=>Ie,InviteServerTransaction:()=>Ee,InviteUserAgentClient:()=>Ve,InviteUserAgentServer:()=>ze,Levels:()=>oe,Logger:()=>ue,LoggerFactory:()=>pe,MessageUserAgentClient:()=>qe,MessageUserAgentServer:()=>xe,NameAddrHeader:()=>m,NonInviteClientTransaction:()=>Se,NonInviteServerTransaction:()=>De,NotifyUserAgentClient:()=>Ne,NotifyUserAgentServer:()=>Me,OutgoingRequestMessage:()=>k,Parameters:()=>f,Parser:()=>st,PrackUserAgentClient:()=>Oe,PrackUserAgentServer:()=>Ue,PublishUserAgentClient:()=>Ke,ReInviteUserAgentClient:()=>je,ReInviteUserAgentServer:()=>Fe,ReSubscribeUserAgentClient:()=>Ye,ReSubscribeUserAgentServer:()=>dt,ReferUserAgentClient:()=>Le,ReferUserAgentServer:()=>Be,RegisterUserAgentClient:()=>We,RegisterUserAgentServer:()=>Xe,ServerTransaction:()=>Re,SessionDialog:()=>Ge,SessionState:()=>N,SignalingState:()=>M,SubscribeUserAgentClient:()=>Je,SubscribeUserAgentServer:()=>Qe,SubscriptionDialog:()=>Ze,SubscriptionState:()=>se,Timers:()=>F,Transaction:()=>Te,TransactionState:()=>ae,TransactionStateError:()=>O,TransportError:()=>be,URI:()=>v,UserAgentClient:()=>Ce,UserAgentCore:()=>tt,UserAgentServer:()=>He,constructOutgoingResponse:()=>we,equivalentURI:()=>w,fromBodyLegacy:()=>P,getBody:()=>x,isBody:()=>q});var i={};e.r(i),e.d(i,{SessionDescriptionHandler:()=>nt,SimpleUser:()=>Tt,Transport:()=>at,addMidLines:()=>wt,cleanJitsiSdpImageattr:()=>pt,defaultMediaStreamFactory:()=>it,defaultPeerConnectionConfiguration:()=>rt,defaultSessionDescriptionHandlerFactory:()=>ot,holdModifier:()=>bt,stripG722:()=>ft,stripRtpPayload:()=>mt,stripTcpCandidates:()=>gt,stripTelephoneEvent:()=>ut,stripVideo:()=>vt});const r="0.20.0";class n extends Error{constructor(e){super(e),Object.setPrototypeOf(this,new.target.prototype)}}class o extends n{constructor(e){super(e||"Unsupported content type.")}}class a extends n{constructor(e){super(e||"Request pending.")}}class c extends n{constructor(e){super(e||"Unspecified session description handler error.")}}class h extends n{constructor(){super("The session has terminated.")}}class d extends n{constructor(e){super(e||"An error occurred during state transition.")}}class l{constructor(e){this.incomingAckRequest=e}get request(){return this.incomingAckRequest.message}}class g{constructor(e){this.incomingByeRequest=e}get request(){return this.incomingByeRequest.message}accept(e){return this.incomingByeRequest.accept(e),Promise.resolve()}reject(e){return this.incomingByeRequest.reject(e),Promise.resolve()}}class u{constructor(){this.listeners=new Array}addListener(e,t){const s=t=>{this.removeListener(s),e(t)};!0===(null==t?void 0:t.once)?this.listeners.push(s):this.listeners.push(e)}emit(e){this.listeners.slice().forEach((t=>t(e)))}removeAllListeners(){this.listeners=[]}removeListener(e){this.listeners=this.listeners.filter((t=>t!==e))}on(e){return this.addListener(e)}off(e){return this.removeListener(e)}once(e){return this.addListener(e,{once:!0})}}class p{constructor(e){this.incomingInfoRequest=e}get request(){return this.incomingInfoRequest.message}accept(e){return this.incomingInfoRequest.accept(e),Promise.resolve()}reject(e){return this.incomingInfoRequest.reject(e),Promise.resolve()}}class f{constructor(e){this.parameters={};for(const t in e)e.hasOwnProperty(t)&&this.setParam(t,e[t])}setParam(e,t){e&&(this.parameters[e.toLowerCase()]=null==t?null:t.toString())}getParam(e){if(e)return this.parameters[e.toLowerCase()]}hasParam(e){return!(!e||void 0===this.parameters[e.toLowerCase()])}deleteParam(e){if(e=e.toLowerCase(),this.hasParam(e)){const t=this.parameters[e];return delete this.parameters[e],t}}clearParams(){this.parameters={}}}class m extends f{constructor(e,t,s){super(s),this.uri=e,this._displayName=t}get friendlyName(){return this.displayName||this.uri.aor}get displayName(){return this._displayName}set displayName(e){this._displayName=e}clone(){return new m(this.uri.clone(),this._displayName,JSON.parse(JSON.stringify(this.parameters)))}toString(){let e=this.displayName||"0"===this.displayName?'"'+this.displayName+'" ':"";e+="<"+this.uri.toString()+">";for(const t in this.parameters)this.parameters.hasOwnProperty(t)&&(e+=";"+t,null!==this.parameters[t]&&(e+="="+this.parameters[t]));return e}}class v extends f{constructor(e="sip",t,s,i,r,n){if(super(r||{}),this.headers={},!s)throw new TypeError('missing or invalid "host" parameter');for(const e in n)n.hasOwnProperty(e)&&this.setHeader(e,n[e]);this.raw={scheme:e,user:t,host:s,port:i},this.normal={scheme:e.toLowerCase(),user:t,host:s.toLowerCase(),port:i}}get scheme(){return this.normal.scheme}set scheme(e){this.raw.scheme=e,this.normal.scheme=e.toLowerCase()}get user(){return this.normal.user}set user(e){this.normal.user=this.raw.user=e}get host(){return this.normal.host}set host(e){this.raw.host=e,this.normal.host=e.toLowerCase()}get aor(){return this.normal.user+"@"+this.normal.host}get port(){return this.normal.port}set port(e){this.normal.port=this.raw.port=e}setHeader(e,t){this.headers[this.headerize(e)]=t instanceof Array?t:[t]}getHeader(e){if(e)return this.headers[this.headerize(e)]}hasHeader(e){return!!e&&!!this.headers.hasOwnProperty(this.headerize(e))}deleteHeader(e){if(e=this.headerize(e),this.headers.hasOwnProperty(e)){const t=this.headers[e];return delete this.headers[e],t}}clearHeaders(){this.headers={}}clone(){return new v(this._raw.scheme,this._raw.user||"",this._raw.host,this._raw.port,JSON.parse(JSON.stringify(this.parameters)),JSON.parse(JSON.stringify(this.headers)))}toRaw(){return this._toString(this._raw)}toString(){return this._toString(this._normal)}get _normal(){return this.normal}get _raw(){return this.raw}_toString(e){let t=e.scheme+":";e.scheme.toLowerCase().match("^sips?$")||(t+="//"),e.user&&(t+=this.escapeUser(e.user)+"@"),t+=e.host,(e.port||0===e.port)&&(t+=":"+e.port);for(const e in this.parameters)this.parameters.hasOwnProperty(e)&&(t+=";"+e,null!==this.parameters[e]&&(t+="="+this.parameters[e]));const s=[];for(const e in this.headers)if(this.headers.hasOwnProperty(e))for(const t in this.headers[e])this.headers[e].hasOwnProperty(t)&&s.push(e+"="+this.headers[e][t]);return s.length>0&&(t+="?"+s.join("&")),t}escapeUser(e){let t;try{t=decodeURIComponent(e)}catch(e){throw e}return encodeURIComponent(t).replace(/%3A/gi,":").replace(/%2B/gi,"+").replace(/%3F/gi,"?").replace(/%2F/gi,"/")}headerize(e){const t={"Call-Id":"Call-ID",Cseq:"CSeq","Min-Se":"Min-SE",Rack:"RAck",Rseq:"RSeq","Www-Authenticate":"WWW-Authenticate"},s=e.toLowerCase().replace(/_/g,"-").split("-"),i=s.length;let r="";for(let e=0;e<i;e++)0!==e&&(r+="-"),r+=s[e].charAt(0).toUpperCase()+s[e].substring(1);return t[r]&&(r=t[r]),r}}function w(e,t){if(e.scheme!==t.scheme)return!1;if(e.user!==t.user||e.host!==t.host||e.port!==t.port)return!1;if(!function(e,t){const s=Object.keys(e.parameters),i=Object.keys(t.parameters);return!!s.filter((e=>i.includes(e))).every((s=>e.parameters[s]===t.parameters[s]))&&(!!["user","ttl","method","transport"].every((s=>e.hasParam(s)&&t.hasParam(s)||!e.hasParam(s)&&!t.hasParam(s)))&&!!["maddr"].every((s=>e.hasParam(s)&&t.hasParam(s)||!e.hasParam(s)&&!t.hasParam(s))))}(e,t))return!1;const s=Object.keys(e.headers),i=Object.keys(t.headers);if(0!==s.length||0!==i.length){if(s.length!==i.length)return!1;const r=s.filter((e=>i.includes(e)));if(r.length!==i.length)return!1;if(!r.every((s=>e.headers[s].length&&t.headers[s].length&&e.headers[s][0]===t.headers[s][0])))return!1}return!0}class b extends Error{constructor(e,t,s,i){super(),this.message=e,this.expected=t,this.found=s,this.location=i,this.name="SyntaxError","function"==typeof Error.captureStackTrace&&Error.captureStackTrace(this,b)}static buildMessage(e,t){function s(e){return e.charCodeAt(0).toString(16).toUpperCase()}function i(e){return e.replace(/\\/g,"\\\\").replace(/"/g,'\\"').replace(/\0/g,"\\0").replace(/\t/g,"\\t").replace(/\n/g,"\\n").replace(/\r/g,"\\r").replace(/[\x00-\x0F]/g,(e=>"\\x0"+s(e))).replace(/[\x10-\x1F\x7F-\x9F]/g,(e=>"\\x"+s(e)))}function r(e){return e.replace(/\\/g,"\\\\").replace(/\]/g,"\\]").replace(/\^/g,"\\^").replace(/-/g,"\\-").replace(/\0/g,"\\0").replace(/\t/g,"\\t").replace(/\n/g,"\\n").replace(/\r/g,"\\r").replace(/[\x00-\x0F]/g,(e=>"\\x0"+s(e))).replace(/[\x10-\x1F\x7F-\x9F]/g,(e=>"\\x"+s(e)))}function n(e){switch(e.type){case"literal":return'"'+i(e.text)+'"';case"class":const t=e.parts.map((e=>Array.isArray(e)?r(e[0])+"-"+r(e[1]):r(e)));return"["+(e.inverted?"^":"")+t+"]";case"any":return"any character";case"end":return"end of input";case"other":return e.description}}return"Expected "+function(e){const t=e.map(n);let s,i;if(t.sort(),t.length>0){for(s=1,i=1;s<t.length;s++)t[s-1]!==t[s]&&(t[i]=t[s],i++);t.length=i}switch(t.length){case 1:return t[0];case 2:return t[0]+" or "+t[1];default:return t.slice(0,-1).join(", ")+", or "+t[t.length-1]}}(e)+" but "+(((o=t)?'"'+i(o)+'"':"end of input")+" found.");var o}}const T=function(e,t){t=void 0!==t?t:{};const s={},i={Contact:119,Name_Addr_Header:156,Record_Route:176,Request_Response:81,SIP_URI:45,Subscription_State:186,Supported:191,Require:182,Via:194,absoluteURI:84,Call_ID:118,Content_Disposition:130,Content_Length:135,Content_Type:136,CSeq:146,displayName:122,Event:149,From:151,host:52,Max_Forwards:154,Min_SE:213,Proxy_Authenticate:157,quoted_string:40,Refer_To:178,Replaces:179,Session_Expires:210,stun_URI:217,To:192,turn_URI:223,uuid:226,WWW_Authenticate:209,challenge:158,sipfrag:230,Referred_By:231};let r=119;const n=["\r\n",w("\r\n",!1),/^[0-9]/,T([["0","9"]],!1,!1),/^[a-zA-Z]/,T([["a","z"],["A","Z"]],!1,!1),/^[0-9a-fA-F]/,T([["0","9"],["a","f"],["A","F"]],!1,!1),/^[\0-\xFF]/,T([["\0","\xff"]],!1,!1),/^["]/,T(['"'],!1,!1)," ",w(" ",!1),"\t",w("\t",!1),/^[a-zA-Z0-9]/,T([["a","z"],["A","Z"],["0","9"]],!1,!1),";",w(";",!1),"/",w("/",!1),"?",w("?",!1),":",w(":",!1),"@",w("@",!1),"&",w("&",!1),"=",w("=",!1),"+",w("+",!1),"$",w("$",!1),",",w(",",!1),"-",w("-",!1),"_",w("_",!1),".",w(".",!1),"!",w("!",!1),"~",w("~",!1),"*",w("*",!1),"'",w("'",!1),"(",w("(",!1),")",w(")",!1),"%",w("%",!1),function(){return" "},function(){return":"},/^[!-~]/,T([["!","~"]],!1,!1),/^[\x80-\uFFFF]/,T([["\x80","\uffff"]],!1,!1),/^[\x80-\xBF]/,T([["\x80","\xbf"]],!1,!1),/^[a-f]/,T([["a","f"]],!1,!1),"`",w("`",!1),"<",w("<",!1),">",w(">",!1),"\\",w("\\",!1),"[",w("[",!1),"]",w("]",!1),"{",w("{",!1),"}",w("}",!1),function(){return"*"},function(){return"/"},function(){return"="},function(){return"("},function(){return")"},function(){return">"},function(){return"<"},function(){return","},function(){return";"},function(){return":"},function(){return'"'},/^[!-']/,T([["!","'"]],!1,!1),/^[*-[]/,T([["*","["]],!1,!1),/^[\]-~]/,T([["]","~"]],!1,!1),function(e){return e},/^[#-[]/,T([["#","["]],!1,!1),/^[\0-\t]/,T([["\0","\t"]],!1,!1),/^[\x0B-\f]/,T([["\v","\f"]],!1,!1),/^[\x0E-\x7F]/,T([["\x0e","\x7f"]],!1,!1),function(){(t=t||{data:{}}).data.uri=new v(t.data.scheme,t.data.user,t.data.host,t.data.port),delete t.data.scheme,delete t.data.user,delete t.data.host,delete t.data.host_type,delete t.data.port},function(){(t=t||{data:{}}).data.uri=new v(t.data.scheme,t.data.user,t.data.host,t.data.port,t.data.uri_params,t.data.uri_headers),delete t.data.scheme,delete t.data.user,delete t.data.host,delete t.data.host_type,delete t.data.port,delete t.data.uri_params,"SIP_URI"===t.startRule&&(t.data=t.data.uri)},"sips",w("sips",!0),"sip",w("sip",!0),function(e){(t=t||{data:{}}).data.scheme=e},function(){(t=t||{data:{}}).data.user=decodeURIComponent(p().slice(0,-1))},function(){(t=t||{data:{}}).data.password=p()},function(){return(t=t||{data:{}}).data.host=p(),t.data.host},function(){return(t=t||{data:{}}).data.host_type="domain",p()},/^[a-zA-Z0-9_\-]/,T([["a","z"],["A","Z"],["0","9"],"_","-"],!1,!1),/^[a-zA-Z0-9\-]/,T([["a","z"],["A","Z"],["0","9"],"-"],!1,!1),function(){return(t=t||{data:{}}).data.host_type="IPv6",p()},"::",w("::",!1),function(){return(t=t||{data:{}}).data.host_type="IPv6",p()},function(){return(t=t||{data:{}}).data.host_type="IPv4",p()},"25",w("25",!1),/^[0-5]/,T([["0","5"]],!1,!1),"2",w("2",!1),/^[0-4]/,T([["0","4"]],!1,!1),"1",w("1",!1),/^[1-9]/,T([["1","9"]],!1,!1),function(e){return t=t||{data:{}},e=parseInt(e.join("")),t.data.port=e,e},"transport=",w("transport=",!0),"udp",w("udp",!0),"tcp",w("tcp",!0),"sctp",w("sctp",!0),"tls",w("tls",!0),function(e){(t=t||{data:{}}).data.uri_params||(t.data.uri_params={}),t.data.uri_params.transport=e.toLowerCase()},"user=",w("user=",!0),"phone",w("phone",!0),"ip",w("ip",!0),function(e){(t=t||{data:{}}).data.uri_params||(t.data.uri_params={}),t.data.uri_params.user=e.toLowerCase()},"method=",w("method=",!0),function(e){(t=t||{data:{}}).data.uri_params||(t.data.uri_params={}),t.data.uri_params.method=e},"ttl=",w("ttl=",!0),function(e){(t=t||{data:{}}).data.params||(t.data.params={}),t.data.params.ttl=e},"maddr=",w("maddr=",!0),function(e){(t=t||{data:{}}).data.uri_params||(t.data.uri_params={}),t.data.uri_params.maddr=e},"lr",w("lr",!0),function(){(t=t||{data:{}}).data.uri_params||(t.data.uri_params={}),t.data.uri_params.lr=void 0},function(e,s){(t=t||{data:{}}).data.uri_params||(t.data.uri_params={}),s=null===s?void 0:s[1],t.data.uri_params[e.toLowerCase()]=s},function(e,s){e=e.join("").toLowerCase(),s=s.join(""),(t=t||{data:{}}).data.uri_headers||(t.data.uri_headers={}),t.data.uri_headers[e]?t.data.uri_headers[e].push(s):t.data.uri_headers[e]=[s]},function(){"Refer_To"===(t=t||{data:{}}).startRule&&(t.data.uri=new v(t.data.scheme,t.data.user,t.data.host,t.data.port,t.data.uri_params,t.data.uri_headers),delete t.data.scheme,delete t.data.user,delete t.data.host,delete t.data.host_type,delete t.data.port,delete t.data.uri_params)},"//",w("//",!1),function(){(t=t||{data:{}}).data.scheme=p()},w("SIP",!0),function(){(t=t||{data:{}}).data.sip_version=p()},"INVITE",w("INVITE",!1),"ACK",w("ACK",!1),"VXACH",w("VXACH",!1),"OPTIONS",w("OPTIONS",!1),"BYE",w("BYE",!1),"CANCEL",w("CANCEL",!1),"REGISTER",w("REGISTER",!1),"SUBSCRIBE",w("SUBSCRIBE",!1),"NOTIFY",w("NOTIFY",!1),"REFER",w("REFER",!1),"PUBLISH",w("PUBLISH",!1),function(){return(t=t||{data:{}}).data.method=p(),t.data.method},function(e){(t=t||{data:{}}).data.status_code=parseInt(e.join(""))},function(){(t=t||{data:{}}).data.reason_phrase=p()},function(){(t=t||{data:{}}).data=p()},function(){var e,s;for(s=(t=t||{data:{}}).data.multi_header.length,e=0;e<s;e++)if(null===t.data.multi_header[e].parsed){t.data=null;break}null!==t.data?t.data=t.data.multi_header:t.data=-1},function(){var e;(t=t||{data:{}}).data.multi_header||(t.data.multi_header=[]);try{e=new m(t.data.uri,t.data.displayName,t.data.params),delete t.data.uri,delete t.data.displayName,delete t.data.params}catch(t){e=null}t.data.multi_header.push({position:a,offset:f().start.offset,parsed:e})},function(e){'"'===(e=p().trim())[0]&&(e=e.substring(1,e.length-1)),(t=t||{data:{}}).data.displayName=e},"q",w("q",!0),function(e){(t=t||{data:{}}).data.params||(t.data.params={}),t.data.params.q=e},"expires",w("expires",!0),function(e){(t=t||{data:{}}).data.params||(t.data.params={}),t.data.params.expires=e},function(e){return parseInt(e.join(""))},"0",w("0",!1),function(){return parseFloat(p())},function(e,s){(t=t||{data:{}}).data.params||(t.data.params={}),s=null===s?void 0:s[1],t.data.params[e.toLowerCase()]=s},"render",w("render",!0),"session",w("session",!0),"icon",w("icon",!0),"alert",w("alert",!0),function(){"Content_Disposition"===(t=t||{data:{}}).startRule&&(t.data.type=p().toLowerCase())},"handling",w("handling",!0),"optional",w("optional",!0),"required",w("required",!0),function(e){(t=t||{data:{}}).data=parseInt(e.join(""))},function(){(t=t||{data:{}}).data=p()},"text",w("text",!0),"image",w("image",!0),"audio",w("audio",!0),"video",w("video",!0),"application",w("application",!0),"message",w("message",!0),"multipart",w("multipart",!0),"x-",w("x-",!0),function(e){(t=t||{data:{}}).data.value=parseInt(e.join(""))},function(e){(t=t||{data:{}}).data=e},function(e){(t=t||{data:{}}).data.event=e.toLowerCase()},function(){var e=(t=t||{data:{}}).data.tag;t.data=new m(t.data.uri,t.data.displayName,t.data.params),e&&t.data.setParam("tag",e)},"tag",w("tag",!0),function(e){(t=t||{data:{}}).data.tag=e},function(e){(t=t||{data:{}}).data=parseInt(e.join(""))},function(e){(t=t||{data:{}}).data=e},function(){(t=t||{data:{}}).data=new m(t.data.uri,t.data.displayName,t.data.params)},"digest",w("Digest",!0),"realm",w("realm",!0),function(e){(t=t||{data:{}}).data.realm=e},"domain",w("domain",!0),"nonce",w("nonce",!0),function(e){(t=t||{data:{}}).data.nonce=e},"opaque",w("opaque",!0),function(e){(t=t||{data:{}}).data.opaque=e},"stale",w("stale",!0),"true",w("true",!0),function(){(t=t||{data:{}}).data.stale=!0},"false",w("false",!0),function(){(t=t||{data:{}}).data.stale=!1},"algorithm",w("algorithm",!0),"md5",w("MD5",!0),"md5-sess",w("MD5-sess",!0),function(e){(t=t||{data:{}}).data.algorithm=e.toUpperCase()},"qop",w("qop",!0),"auth-int",w("auth-int",!0),"auth",w("auth",!0),function(e){(t=t||{data:{}}).data.qop||(t.data.qop=[]),t.data.qop.push(e.toLowerCase())},function(e){(t=t||{data:{}}).data.value=parseInt(e.join(""))},function(){var e,s;for(s=(t=t||{data:{}}).data.multi_header.length,e=0;e<s;e++)if(null===t.data.multi_header[e].parsed){t.data=null;break}null!==t.data?t.data=t.data.multi_header:t.data=-1},function(){var e;(t=t||{data:{}}).data.multi_header||(t.data.multi_header=[]);try{e=new m(t.data.uri,t.data.displayName,t.data.params),delete t.data.uri,delete t.data.displayName,delete t.data.params}catch(t){e=null}t.data.multi_header.push({position:a,offset:f().start.offset,parsed:e})},function(){(t=t||{data:{}}).data=new m(t.data.uri,t.data.displayName,t.data.params)},function(){(t=t||{data:{}}).data.replaces_from_tag&&t.data.replaces_to_tag||(t.data=-1)},function(){(t=t||{data:{}}).data={call_id:t.data}},"from-tag",w("from-tag",!0),function(e){(t=t||{data:{}}).data.replaces_from_tag=e},"to-tag",w("to-tag",!0),function(e){(t=t||{data:{}}).data.replaces_to_tag=e},"early-only",w("early-only",!0),function(){(t=t||{data:{}}).data.early_only=!0},function(e,t){return t},function(e,t){return function(e,t){return[e].concat(t)}(e,t)},function(e){"Require"===(t=t||{data:{}}).startRule&&(t.data=e||[])},function(e){(t=t||{data:{}}).data.value=parseInt(e.join(""))},"active",w("active",!0),"pending",w("pending",!0),"terminated",w("terminated",!0),function(){(t=t||{data:{}}).data.state=p()},"reason",w("reason",!0),function(e){t=t||{data:{}},void 0!==e&&(t.data.reason=e)},function(e){t=t||{data:{}},void 0!==e&&(t.data.expires=e)},"retry_after",w("retry_after",!0),function(e){t=t||{data:{}},void 0!==e&&(t.data.retry_after=e)},"deactivated",w("deactivated",!0),"probation",w("probation",!0),"rejected",w("rejected",!0),"timeout",w("timeout",!0),"giveup",w("giveup",!0),"noresource",w("noresource",!0),"invariant",w("invariant",!0),function(e){"Supported"===(t=t||{data:{}}).startRule&&(t.data=e||[])},function(){var e=(t=t||{data:{}}).data.tag;t.data=new m(t.data.uri,t.data.displayName,t.data.params),e&&t.data.setParam("tag",e)},"ttl",w("ttl",!0),function(e){(t=t||{data:{}}).data.ttl=e},"maddr",w("maddr",!0),function(e){(t=t||{data:{}}).data.maddr=e},"received",w("received",!0),function(e){(t=t||{data:{}}).data.received=e},"branch",w("branch",!0),function(e){(t=t||{data:{}}).data.branch=e},"rport",w("rport",!0),function(e){t=t||{data:{}},void 0!==e&&(t.data.rport=e.join(""))},function(e){(t=t||{data:{}}).data.protocol=e},w("UDP",!0),w("TCP",!0),w("TLS",!0),w("SCTP",!0),function(e){(t=t||{data:{}}).data.transport=e},function(){(t=t||{data:{}}).data.host=p()},function(e){(t=t||{data:{}}).data.port=parseInt(e.join(""))},function(e){return parseInt(e.join(""))},function(e){"Session_Expires"===(t=t||{data:{}}).startRule&&(t.data.deltaSeconds=e)},"refresher",w("refresher",!1),"uas",w("uas",!1),"uac",w("uac",!1),function(e){"Session_Expires"===(t=t||{data:{}}).startRule&&(t.data.refresher=e)},function(e){"Min_SE"===(t=t||{data:{}}).startRule&&(t.data=e)},"stuns",w("stuns",!0),"stun",w("stun",!0),function(e){(t=t||{data:{}}).data.scheme=e},function(e){(t=t||{data:{}}).data.host=e},"?transport=",w("?transport=",!1),"turns",w("turns",!0),"turn",w("turn",!0),function(e){(t=t||{data:{}}).data.transport=e},function(){(t=t||{data:{}}).data=p()},"Referred-By",w("Referred-By",!1),"b",w("b",!1),"cid",w("cid",!1)],o=[$('2 ""6 7!'),$('4"""5!7#'),$('4$""5!7%'),$('4&""5!7\''),$(";'.# &;("),$('4(""5!7)'),$('4*""5!7+'),$('2,""6,7-'),$('2.""6.7/'),$('40""5!71'),$('22""6273.\x89 &24""6475.} &26""6677.q &28""6879.e &2:""6:7;.Y &2<""6<7=.M &2>""6>7?.A &2@""6@7A.5 &2B""6B7C.) &2D""6D7E'),$(";).# &;,"),$('2F""6F7G.} &2H""6H7I.q &2J""6J7K.e &2L""6L7M.Y &2N""6N7O.M &2P""6P7Q.A &2R""6R7S.5 &2T""6T7U.) &2V""6V7W'),$('%%2X""6X7Y/5#;#/,$;#/#$+#)(#\'#("\'#&\'#/"!&,)'),$('%%$;$0#*;$&/,#; /#$+")("\'#&\'#." &"/=#$;$/&#0#*;$&&&#/\'$8":Z" )("\'#&\'#'),$(';.." &"'),$("%$;'.# &;(0)*;'.# &;(&/?#28\"\"6879/0$;//'$8#:[# )(#'#(\"'#&'#"),$('%%$;2/&#0#*;2&&&#/g#$%$;.0#*;.&/,#;2/#$+")("\'#&\'#0=*%$;.0#*;.&/,#;2/#$+")("\'#&\'#&/#$+")("\'#&\'#/"!&,)'),$('4\\""5!7].# &;3'),$('4^""5!7_'),$('4`""5!7a'),$(';!.) &4b""5!7c'),$('%$;).\x95 &2F""6F7G.\x89 &2J""6J7K.} &2L""6L7M.q &2X""6X7Y.e &2P""6P7Q.Y &2H""6H7I.M &2@""6@7A.A &2d""6d7e.5 &2R""6R7S.) &2N""6N7O/\x9e#0\x9b*;).\x95 &2F""6F7G.\x89 &2J""6J7K.} &2L""6L7M.q &2X""6X7Y.e &2P""6P7Q.Y &2H""6H7I.M &2@""6@7A.A &2d""6d7e.5 &2R""6R7S.) &2N""6N7O&&&#/"!&,)'),$('%$;).\x89 &2F""6F7G.} &2L""6L7M.q &2X""6X7Y.e &2P""6P7Q.Y &2H""6H7I.M &2@""6@7A.A &2d""6d7e.5 &2R""6R7S.) &2N""6N7O/\x92#0\x8f*;).\x89 &2F""6F7G.} &2L""6L7M.q &2X""6X7Y.e &2P""6P7Q.Y &2H""6H7I.M &2@""6@7A.A &2d""6d7e.5 &2R""6R7S.) &2N""6N7O&&&#/"!&,)'),$('2T""6T7U.\xe3 &2V""6V7W.\xd7 &2f""6f7g.\xcb &2h""6h7i.\xbf &2:""6:7;.\xb3 &2D""6D7E.\xa7 &22""6273.\x9b &28""6879.\x8f &2j""6j7k.\x83 &;&.} &24""6475.q &2l""6l7m.e &2n""6n7o.Y &26""6677.M &2>""6>7?.A &2p""6p7q.5 &2r""6r7s.) &;\'.# &;('),$('%$;).\u012b &2F""6F7G.\u011f &2J""6J7K.\u0113 &2L""6L7M.\u0107 &2X""6X7Y.\xfb &2P""6P7Q.\xef &2H""6H7I.\xe3 &2@""6@7A.\xd7 &2d""6d7e.\xcb &2R""6R7S.\xbf &2N""6N7O.\xb3 &2T""6T7U.\xa7 &2V""6V7W.\x9b &2f""6f7g.\x8f &2h""6h7i.\x83 &28""6879.w &2j""6j7k.k &;&.e &24""6475.Y &2l""6l7m.M &2n""6n7o.A &26""6677.5 &2p""6p7q.) &2r""6r7s/\u0134#0\u0131*;).\u012b &2F""6F7G.\u011f &2J""6J7K.\u0113 &2L""6L7M.\u0107 &2X""6X7Y.\xfb &2P""6P7Q.\xef &2H""6H7I.\xe3 &2@""6@7A.\xd7 &2d""6d7e.\xcb &2R""6R7S.\xbf &2N""6N7O.\xb3 &2T""6T7U.\xa7 &2V""6V7W.\x9b &2f""6f7g.\x8f &2h""6h7i.\x83 &28""6879.w &2j""6j7k.k &;&.e &24""6475.Y &2l""6l7m.M &2n""6n7o.A &26""6677.5 &2p""6p7q.) &2r""6r7s&&&#/"!&,)'),$("%;//?#2P\"\"6P7Q/0$;//'$8#:t# )(#'#(\"'#&'#"),$("%;//?#24\"\"6475/0$;//'$8#:u# )(#'#(\"'#&'#"),$("%;//?#2>\"\"6>7?/0$;//'$8#:v# )(#'#(\"'#&'#"),$("%;//?#2T\"\"6T7U/0$;//'$8#:w# )(#'#(\"'#&'#"),$("%;//?#2V\"\"6V7W/0$;//'$8#:x# )(#'#(\"'#&'#"),$('%2h""6h7i/0#;//\'$8":y" )("\'#&\'#'),$('%;//6#2f""6f7g/\'$8":z" )("\'#&\'#'),$("%;//?#2D\"\"6D7E/0$;//'$8#:{# )(#'#(\"'#&'#"),$("%;//?#22\"\"6273/0$;//'$8#:|# )(#'#(\"'#&'#"),$("%;//?#28\"\"6879/0$;//'$8#:}# )(#'#(\"'#&'#"),$("%;//0#;&/'$8\":~\" )(\"'#&'#"),$("%;&/0#;//'$8\":~\" )(\"'#&'#"),$("%;=/T#$;G.) &;K.# &;F0/*;G.) &;K.# &;F&/,$;>/#$+#)(#'#(\"'#&'#"),$('4\x7f""5!7\x80.A &4\x81""5!7\x82.5 &4\x83""5!7\x84.) &;3.# &;.'),$("%%;//Q#;&/H$$;J.# &;K0)*;J.# &;K&/,$;&/#$+$)($'#(#'#(\"'#&'#/\"!&,)"),$("%;//]#;&/T$%$;J.# &;K0)*;J.# &;K&/\"!&,)/1$;&/($8$:\x85$!!)($'#(#'#(\"'#&'#"),$(';..G &2L""6L7M.; &4\x86""5!7\x87./ &4\x83""5!7\x84.# &;3'),$('%2j""6j7k/J#4\x88""5!7\x89.5 &4\x8a""5!7\x8b.) &4\x8c""5!7\x8d/#$+")("\'#&\'#'),$("%;N/M#28\"\"6879/>$;O.\" &\"/0$;S/'$8$:\x8e$ )($'#(#'#(\"'#&'#"),$("%;N/d#28\"\"6879/U$;O.\" &\"/G$;S/>$;_/5$;l.\" &\"/'$8&:\x8f& )(&'#(%'#($'#(#'#(\"'#&'#"),$('%3\x90""5$7\x91.) &3\x92""5#7\x93/\' 8!:\x94!! )'),$('%;P/]#%28""6879/,#;R/#$+")("\'#&\'#." &"/6$2:""6:7;/\'$8#:\x95# )(#\'#("\'#&\'#'),$("$;+.) &;-.# &;Q/2#0/*;+.) &;-.# &;Q&&&#"),$('2<""6<7=.q &2>""6>7?.e &2@""6@7A.Y &2B""6B7C.M &2D""6D7E.A &22""6273.5 &26""6677.) &24""6475'),$('%$;+._ &;-.Y &2<""6<7=.M &2>""6>7?.A &2@""6@7A.5 &2B""6B7C.) &2D""6D7E0e*;+._ &;-.Y &2<""6<7=.M &2>""6>7?.A &2@""6@7A.5 &2B""6B7C.) &2D""6D7E&/& 8!:\x96! )'),$('%;T/J#%28""6879/,#;^/#$+")("\'#&\'#." &"/#$+")("\'#&\'#'),$("%;U.) &;\\.# &;X/& 8!:\x97! )"),$('%$%;V/2#2J""6J7K/#$+")("\'#&\'#0<*%;V/2#2J""6J7K/#$+")("\'#&\'#&/D#;W/;$2J""6J7K." &"/\'$8#:\x98# )(#\'#("\'#&\'#'),$('$4\x99""5!7\x9a/,#0)*4\x99""5!7\x9a&&&#'),$('%4$""5!7%/?#$4\x9b""5!7\x9c0)*4\x9b""5!7\x9c&/#$+")("\'#&\'#'),$('%2l""6l7m/?#;Y/6$2n""6n7o/\'$8#:\x9d# )(#\'#("\'#&\'#'),$('%%;Z/\xb3#28""6879/\xa4$;Z/\x9b$28""6879/\x8c$;Z/\x83$28""6879/t$;Z/k$28""6879/\\$;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$+-)(-\'#(,\'#(+\'#(*\'#()\'#((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u0790 &%2\x9e""6\x9e7\x9f/\xa4#;Z/\x9b$28""6879/\x8c$;Z/\x83$28""6879/t$;Z/k$28""6879/\\$;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$+,)(,\'#(+\'#(*\'#()\'#((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u06f9 &%2\x9e""6\x9e7\x9f/\x8c#;Z/\x83$28""6879/t$;Z/k$28""6879/\\$;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$+*)(*\'#()\'#((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u067a &%2\x9e""6\x9e7\x9f/t#;Z/k$28""6879/\\$;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$+()((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u0613 &%2\x9e""6\x9e7\x9f/\\#;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$+&)(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u05c4 &%2\x9e""6\x9e7\x9f/D#;Z/;$28""6879/,$;[/#$+$)($\'#(#\'#("\'#&\'#.\u058d &%2\x9e""6\x9e7\x9f/,#;[/#$+")("\'#&\'#.\u056e &%2\x9e""6\x9e7\x9f/,#;Z/#$+")("\'#&\'#.\u054f &%;Z/\x9b#2\x9e""6\x9e7\x9f/\x8c$;Z/\x83$28""6879/t$;Z/k$28""6879/\\$;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$++)(+\'#(*\'#()\'#((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u04c7 &%;Z/\xaa#%28""6879/,#;Z/#$+")("\'#&\'#." &"/\x83$2\x9e""6\x9e7\x9f/t$;Z/k$28""6879/\\$;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$+*)(*\'#()\'#((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u0430 &%;Z/\xb9#%28""6879/,#;Z/#$+")("\'#&\'#." &"/\x92$%28""6879/,#;Z/#$+")("\'#&\'#." &"/k$2\x9e""6\x9e7\x9f/\\$;Z/S$28""6879/D$;Z/;$28""6879/,$;[/#$+))()\'#((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u038a &%;Z/\xc8#%28""6879/,#;Z/#$+")("\'#&\'#." &"/\xa1$%28""6879/,#;Z/#$+")("\'#&\'#." &"/z$%28""6879/,#;Z/#$+")("\'#&\'#." &"/S$2\x9e""6\x9e7\x9f/D$;Z/;$28""6879/,$;[/#$+()((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u02d5 &%;Z/\xd7#%28""6879/,#;Z/#$+")("\'#&\'#." &"/\xb0$%28""6879/,#;Z/#$+")("\'#&\'#." &"/\x89$%28""6879/,#;Z/#$+")("\'#&\'#." &"/b$%28""6879/,#;Z/#$+")("\'#&\'#." &"/;$2\x9e""6\x9e7\x9f/,$;[/#$+\')(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u0211 &%;Z/\xfe#%28""6879/,#;Z/#$+")("\'#&\'#." &"/\xd7$%28""6879/,#;Z/#$+")("\'#&\'#." &"/\xb0$%28""6879/,#;Z/#$+")("\'#&\'#." &"/\x89$%28""6879/,#;Z/#$+")("\'#&\'#." &"/b$%28""6879/,#;Z/#$+")("\'#&\'#." &"/;$2\x9e""6\x9e7\x9f/,$;Z/#$+()((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#.\u0126 &%;Z/\u011c#%28""6879/,#;Z/#$+")("\'#&\'#." &"/\xf5$%28""6879/,#;Z/#$+")("\'#&\'#." &"/\xce$%28""6879/,#;Z/#$+")("\'#&\'#." &"/\xa7$%28""6879/,#;Z/#$+")("\'#&\'#." &"/\x80$%28""6879/,#;Z/#$+")("\'#&\'#." &"/Y$%28""6879/,#;Z/#$+")("\'#&\'#." &"/2$2\x9e""6\x9e7\x9f/#$+()((\'#(\'\'#(&\'#(%\'#($\'#(#\'#("\'#&\'#/& 8!:\xa0! )'),$('%;#/M#;#." &"/?$;#." &"/1$;#." &"/#$+$)($\'#(#\'#("\'#&\'#'),$("%;Z/;#28\"\"6879/,$;Z/#$+#)(#'#(\"'#&'#.# &;\\"),$("%;]/o#2J\"\"6J7K/`$;]/W$2J\"\"6J7K/H$;]/?$2J\"\"6J7K/0$;]/'$8':\xa1' )(''#(&'#(%'#($'#(#'#(\"'#&'#"),$('%2\xa2""6\xa27\xa3/2#4\xa4""5!7\xa5/#$+")("\'#&\'#.\x98 &%2\xa6""6\xa67\xa7/;#4\xa8""5!7\xa9/,$;!/#$+#)(#\'#("\'#&\'#.j &%2\xaa""6\xaa7\xab/5#;!/,$;!/#$+#)(#\'#("\'#&\'#.B &%4\xac""5!7\xad/,#;!/#$+")("\'#&\'#.# &;!'),$('%%;!." &"/[#;!." &"/M$;!." &"/?$;!." &"/1$;!." &"/#$+%)(%\'#($\'#(#\'#("\'#&\'#/\' 8!:\xae!! )'),$('$%22""6273/,#;`/#$+")("\'#&\'#0<*%22""6273/,#;`/#$+")("\'#&\'#&'),$(";a.A &;b.; &;c.5 &;d./ &;e.) &;f.# &;g"),$('%3\xaf""5*7\xb0/a#3\xb1""5#7\xb2.G &3\xb3""5#7\xb4.; &3\xb5""5$7\xb6./ &3\xb7""5#7\xb8.# &;6/($8":\xb9"! )("\'#&\'#'),$('%3\xba""5%7\xbb/I#3\xbc""5%7\xbd./ &3\xbe""5"7\xbf.# &;6/($8":\xc0"! )("\'#&\'#'),$('%3\xc1""5\'7\xc2/1#;\x90/($8":\xc3"! )("\'#&\'#'),$('%3\xc4""5$7\xc5/1#;\xf0/($8":\xc6"! )("\'#&\'#'),$('%3\xc7""5&7\xc8/1#;T/($8":\xc9"! )("\'#&\'#'),$('%3\xca""5"7\xcb/N#%2>""6>7?/,#;6/#$+")("\'#&\'#." &"/\'$8":\xcc" )("\'#&\'#'),$('%;h/P#%2>""6>7?/,#;i/#$+")("\'#&\'#." &"/)$8":\xcd""! )("\'#&\'#'),$('%$;j/&#0#*;j&&&#/"!&,)'),$('%$;j/&#0#*;j&&&#/"!&,)'),$(";k.) &;+.# &;-"),$('2l""6l7m.e &2n""6n7o.Y &24""6475.M &28""6879.A &2<""6<7=.5 &2@""6@7A.) &2B""6B7C'),$('%26""6677/n#;m/e$$%2<""6<7=/,#;m/#$+")("\'#&\'#0<*%2<""6<7=/,#;m/#$+")("\'#&\'#&/#$+#)(#\'#("\'#&\'#'),$('%;n/A#2>""6>7?/2$;o/)$8#:\xce#"" )(#\'#("\'#&\'#'),$("$;p.) &;+.# &;-/2#0/*;p.) &;+.# &;-&&&#"),$("$;p.) &;+.# &;-0/*;p.) &;+.# &;-&"),$('2l""6l7m.e &2n""6n7o.Y &24""6475.M &26""6677.A &28""6879.5 &2@""6@7A.) &2B""6B7C'),$(";\x91.# &;r"),$("%;\x90/G#;'/>$;s/5$;'/,$;\x84/#$+%)(%'#($'#(#'#(\"'#&'#"),$(";M.# &;t"),$("%;\x7f/E#28\"\"6879/6$;u.# &;x/'$8#:\xcf# )(#'#(\"'#&'#"),$('%;v.# &;w/J#%26""6677/,#;\x83/#$+")("\'#&\'#." &"/#$+")("\'#&\'#'),$('%2\xd0""6\xd07\xd1/:#;\x80/1$;w." &"/#$+#)(#\'#("\'#&\'#'),$('%24""6475/,#;{/#$+")("\'#&\'#'),$("%;z/3#$;y0#*;y&/#$+\")(\"'#&'#"),$(";*.) &;+.# &;-"),$(';+.\x8f &;-.\x89 &22""6273.} &26""6677.q &28""6879.e &2:""6:7;.Y &2<""6<7=.M &2>""6>7?.A &2@""6@7A.5 &2B""6B7C.) &2D""6D7E'),$('%;|/e#$%24""6475/,#;|/#$+")("\'#&\'#0<*%24""6475/,#;|/#$+")("\'#&\'#&/#$+")("\'#&\'#'),$('%$;~0#*;~&/e#$%22""6273/,#;}/#$+")("\'#&\'#0<*%22""6273/,#;}/#$+")("\'#&\'#&/#$+")("\'#&\'#'),$("$;~0#*;~&"),$(';+.w &;-.q &28""6879.e &2:""6:7;.Y &2<""6<7=.M &2>""6>7?.A &2@""6@7A.5 &2B""6B7C.) &2D""6D7E'),$('%%;"/\x87#$;".G &;!.A &2@""6@7A.5 &2F""6F7G.) &2J""6J7K0M*;".G &;!.A &2@""6@7A.5 &2F""6F7G.) &2J""6J7K&/#$+")("\'#&\'#/& 8!:\xd2! )'),$(";\x81.# &;\x82"),$('%%;O/2#2:""6:7;/#$+")("\'#&\'#." &"/,#;S/#$+")("\'#&\'#." &"'),$('$;+.\x83 &;-.} &2B""6B7C.q &2D""6D7E.e &22""6273.Y &28""6879.M &2:""6:7;.A &2<""6<7=.5 &2>""6>7?.) &2@""6@7A/\x8c#0\x89*;+.\x83 &;-.} &2B""6B7C.q &2D""6D7E.e &22""6273.Y &28""6879.M &2:""6:7;.A &2<""6<7=.5 &2>""6>7?.) &2@""6@7A&&&#'),$("$;y0#*;y&"),$('%3\x92""5#7\xd3/q#24""6475/b$$;!/&#0#*;!&&&#/L$2J""6J7K/=$$;!/&#0#*;!&&&#/\'$8%:\xd4% )(%\'#($\'#(#\'#("\'#&\'#'),$('2\xd5""6\xd57\xd6'),$('2\xd7""6\xd77\xd8'),$('2\xd9""6\xd97\xda'),$('2\xdb""6\xdb7\xdc'),$('2\xdd""6\xdd7\xde'),$('2\xdf""6\xdf7\xe0'),$('2\xe1""6\xe17\xe2'),$('2\xe3""6\xe37\xe4'),$('2\xe5""6\xe57\xe6'),$('2\xe7""6\xe77\xe8'),$('2\xe9""6\xe97\xea'),$("%;\x85.Y &;\x86.S &;\x88.M &;\x89.G &;\x8a.A &;\x8b.; &;\x8c.5 &;\x8f./ &;\x8d.) &;\x8e.# &;6/& 8!:\xeb! )"),$("%;\x84/G#;'/>$;\x92/5$;'/,$;\x94/#$+%)(%'#($'#(#'#(\"'#&'#"),$("%;\x93/' 8!:\xec!! )"),$("%;!/5#;!/,$;!/#$+#)(#'#(\"'#&'#"),$("%$;*.A &;+.; &;-.5 &;3./ &;4.) &;'.# &;(0G*;*.A &;+.; &;-.5 &;3./ &;4.) &;'.# &;(&/& 8!:\xed! )"),$("%;\xb6/Y#$%;A/,#;\xb6/#$+\")(\"'#&'#06*%;A/,#;\xb6/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$('%;9/N#%2:""6:7;/,#;9/#$+")("\'#&\'#." &"/\'$8":\xee" )("\'#&\'#'),$("%;:.c &%;\x98/Y#$%;A/,#;\x98/#$+\")(\"'#&'#06*%;A/,#;\x98/#$+\")(\"'#&'#&/#$+\")(\"'#&'#/& 8!:\xef! )"),$("%;L.# &;\x99/]#$%;B/,#;\x9b/#$+\")(\"'#&'#06*%;B/,#;\x9b/#$+\")(\"'#&'#&/'$8\":\xf0\" )(\"'#&'#"),$("%;\x9a.\" &\"/>#;@/5$;M/,$;?/#$+$)($'#(#'#(\"'#&'#"),$("%%;6/Y#$%;./,#;6/#$+\")(\"'#&'#06*%;./,#;6/#$+\")(\"'#&'#&/#$+\")(\"'#&'#.# &;H/' 8!:\xf1!! )"),$(";\x9c.) &;\x9d.# &;\xa0"),$("%3\xf2\"\"5!7\xf3/:#;</1$;\x9f/($8#:\xf4#! )(#'#(\"'#&'#"),$("%3\xf5\"\"5'7\xf6/:#;</1$;\x9e/($8#:\xf7#! )(#'#(\"'#&'#"),$("%$;!/&#0#*;!&&&#/' 8!:\xf8!! )"),$('%2\xf9""6\xf97\xfa/o#%2J""6J7K/M#;!." &"/?$;!." &"/1$;!." &"/#$+$)($\'#(#\'#("\'#&\'#." &"/\'$8":\xfb" )("\'#&\'#'),$('%;6/J#%;</,#;\xa1/#$+")("\'#&\'#." &"/)$8":\xfc""! )("\'#&\'#'),$(";6.) &;T.# &;H"),$("%;\xa3/Y#$%;B/,#;\xa4/#$+\")(\"'#&'#06*%;B/,#;\xa4/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$('%3\xfd""5&7\xfe.G &3\xff""5\'7\u0100.; &3\u0101""5$7\u0102./ &3\u0103""5%7\u0104.# &;6/& 8!:\u0105! )'),$(";\xa5.# &;\xa0"),$('%3\u0106""5(7\u0107/M#;</D$3\u0108""5(7\u0109./ &3\u010a""5(7\u010b.# &;6/#$+#)(#\'#("\'#&\'#'),$("%;6/Y#$%;A/,#;6/#$+\")(\"'#&'#06*%;A/,#;6/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$("%$;!/&#0#*;!&&&#/' 8!:\u010c!! )"),$("%;\xa9/& 8!:\u010d! )"),$("%;\xaa/k#;;/b$;\xaf/Y$$%;B/,#;\xb0/#$+\")(\"'#&'#06*%;B/,#;\xb0/#$+\")(\"'#&'#&/#$+$)($'#(#'#(\"'#&'#"),$(";\xab.# &;\xac"),$('3\u010e""5$7\u010f.S &3\u0110""5%7\u0111.G &3\u0112""5%7\u0113.; &3\u0114""5%7\u0115./ &3\u0116""5+7\u0117.# &;\xad'),$('3\u0118""5\'7\u0119./ &3\u011a""5)7\u011b.# &;\xad'),$(";6.# &;\xae"),$('%3\u011c""5"7\u011d/,#;6/#$+")("\'#&\'#'),$(";\xad.# &;6"),$("%;6/5#;</,$;\xb1/#$+#)(#'#(\"'#&'#"),$(";6.# &;H"),$("%;\xb3/5#;./,$;\x90/#$+#)(#'#(\"'#&'#"),$("%$;!/&#0#*;!&&&#/' 8!:\u011e!! )"),$("%;\x9e/' 8!:\u011f!! )"),$('%;\xb6/^#$%;B/,#;\xa0/#$+")("\'#&\'#06*%;B/,#;\xa0/#$+")("\'#&\'#&/($8":\u0120"!!)("\'#&\'#'),$('%%;7/e#$%2J""6J7K/,#;7/#$+")("\'#&\'#0<*%2J""6J7K/,#;7/#$+")("\'#&\'#&/#$+")("\'#&\'#/"!&,)'),$("%;L.# &;\x99/]#$%;B/,#;\xb8/#$+\")(\"'#&'#06*%;B/,#;\xb8/#$+\")(\"'#&'#&/'$8\":\u0121\" )(\"'#&'#"),$(";\xb9.# &;\xa0"),$("%3\u0122\"\"5#7\u0123/:#;</1$;6/($8#:\u0124#! )(#'#(\"'#&'#"),$("%$;!/&#0#*;!&&&#/' 8!:\u0125!! )"),$("%;\x9e/' 8!:\u0126!! )"),$("%$;\x9a0#*;\x9a&/x#;@/o$;M/f$;?/]$$%;B/,#;\xa0/#$+\")(\"'#&'#06*%;B/,#;\xa0/#$+\")(\"'#&'#&/'$8%:\u0127% )(%'#($'#(#'#(\"'#&'#"),$(";\xbe"),$("%3\u0128\"\"5&7\u0129/k#;./b$;\xc1/Y$$%;A/,#;\xc1/#$+\")(\"'#&'#06*%;A/,#;\xc1/#$+\")(\"'#&'#&/#$+$)($'#(#'#(\"'#&'#.# &;\xbf"),$("%;6/k#;./b$;\xc0/Y$$%;A/,#;\xc0/#$+\")(\"'#&'#06*%;A/,#;\xc0/#$+\")(\"'#&'#&/#$+$)($'#(#'#(\"'#&'#"),$("%;6/;#;</2$;6.# &;H/#$+#)(#'#(\"'#&'#"),$(";\xc2.G &;\xc4.A &;\xc6.; &;\xc8.5 &;\xc9./ &;\xca.) &;\xcb.# &;\xc0"),$("%3\u012a\"\"5%7\u012b/5#;</,$;\xc3/#$+#)(#'#(\"'#&'#"),$("%;I/' 8!:\u012c!! )"),$("%3\u012d\"\"5&7\u012e/\x97#;</\x8e$;D/\x85$;\xc5/|$$%$;'/&#0#*;'&&&#/,#;\xc5/#$+\")(\"'#&'#0C*%$;'/&#0#*;'&&&#/,#;\xc5/#$+\")(\"'#&'#&/,$;E/#$+&)(&'#(%'#($'#(#'#(\"'#&'#"),$(";t.# &;w"),$("%3\u012f\"\"5%7\u0130/5#;</,$;\xc7/#$+#)(#'#(\"'#&'#"),$("%;I/' 8!:\u0131!! )"),$("%3\u0132\"\"5&7\u0133/:#;</1$;I/($8#:\u0134#! )(#'#(\"'#&'#"),$('%3\u0135""5%7\u0136/]#;</T$%3\u0137""5$7\u0138/& 8!:\u0139! ).4 &%3\u013a""5%7\u013b/& 8!:\u013c! )/#$+#)(#\'#("\'#&\'#'),$('%3\u013d""5)7\u013e/R#;</I$3\u013f""5#7\u0140./ &3\u0141""5(7\u0142.# &;6/($8#:\u0143#! )(#\'#("\'#&\'#'),$('%3\u0144""5#7\u0145/\x93#;</\x8a$;D/\x81$%;\xcc/e#$%2D""6D7E/,#;\xcc/#$+")("\'#&\'#0<*%2D""6D7E/,#;\xcc/#$+")("\'#&\'#&/#$+")("\'#&\'#/,$;E/#$+%)(%\'#($\'#(#\'#("\'#&\'#'),$('%3\u0146""5(7\u0147./ &3\u0148""5$7\u0149.# &;6/\' 8!:\u014a!! )'),$("%;6/Y#$%;A/,#;6/#$+\")(\"'#&'#06*%;A/,#;6/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$("%;\xcf/G#;./>$;\xcf/5$;./,$;\x90/#$+%)(%'#($'#(#'#(\"'#&'#"),$("%$;!/&#0#*;!&&&#/' 8!:\u014b!! )"),$("%;\xd1/]#$%;A/,#;\xd1/#$+\")(\"'#&'#06*%;A/,#;\xd1/#$+\")(\"'#&'#&/'$8\":\u014c\" )(\"'#&'#"),$("%;\x99/]#$%;B/,#;\xa0/#$+\")(\"'#&'#06*%;B/,#;\xa0/#$+\")(\"'#&'#&/'$8\":\u014d\" )(\"'#&'#"),$('%;L.O &;\x99.I &%;@." &"/:#;t/1$;?." &"/#$+#)(#\'#("\'#&\'#/]#$%;B/,#;\xa0/#$+")("\'#&\'#06*%;B/,#;\xa0/#$+")("\'#&\'#&/\'$8":\u014e" )("\'#&\'#'),$("%;\xd4/]#$%;B/,#;\xd5/#$+\")(\"'#&'#06*%;B/,#;\xd5/#$+\")(\"'#&'#&/'$8\":\u014f\" )(\"'#&'#"),$("%;\x96/& 8!:\u0150! )"),$('%3\u0151""5(7\u0152/:#;</1$;6/($8#:\u0153#! )(#\'#("\'#&\'#.g &%3\u0154""5&7\u0155/:#;</1$;6/($8#:\u0156#! )(#\'#("\'#&\'#.: &%3\u0157""5*7\u0158/& 8!:\u0159! ).# &;\xa0'),$('%%;6/k#$%;A/2#;6/)$8":\u015a""$ )("\'#&\'#0<*%;A/2#;6/)$8":\u015a""$ )("\'#&\'#&/)$8":\u015b""! )("\'#&\'#." &"/\' 8!:\u015c!! )'),$("%;\xd8/Y#$%;A/,#;\xd8/#$+\")(\"'#&'#06*%;A/,#;\xd8/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$("%;\x99/Y#$%;B/,#;\xa0/#$+\")(\"'#&'#06*%;B/,#;\xa0/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$("%$;!/&#0#*;!&&&#/' 8!:\u015d!! )"),$("%;\xdb/Y#$%;B/,#;\xdc/#$+\")(\"'#&'#06*%;B/,#;\xdc/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$('%3\u015e""5&7\u015f.; &3\u0160""5\'7\u0161./ &3\u0162""5*7\u0163.# &;6/& 8!:\u0164! )'),$("%3\u0165\"\"5&7\u0166/:#;</1$;\xdd/($8#:\u0167#! )(#'#(\"'#&'#.} &%3\xf5\"\"5'7\xf6/:#;</1$;\x9e/($8#:\u0168#! )(#'#(\"'#&'#.P &%3\u0169\"\"5+7\u016a/:#;</1$;\x9e/($8#:\u016b#! )(#'#(\"'#&'#.# &;\xa0"),$('3\u016c""5+7\u016d.k &3\u016e""5)7\u016f._ &3\u0170""5(7\u0171.S &3\u0172""5\'7\u0173.G &3\u0174""5&7\u0175.; &3\u0176""5*7\u0177./ &3\u0178""5)7\u0179.# &;6'),$(';1." &"'),$('%%;6/k#$%;A/2#;6/)$8":\u015a""$ )("\'#&\'#0<*%;A/2#;6/)$8":\u015a""$ )("\'#&\'#&/)$8":\u015b""! )("\'#&\'#." &"/\' 8!:\u017a!! )'),$("%;L.# &;\x99/]#$%;B/,#;\xe1/#$+\")(\"'#&'#06*%;B/,#;\xe1/#$+\")(\"'#&'#&/'$8\":\u017b\" )(\"'#&'#"),$(";\xb9.# &;\xa0"),$("%;\xe3/Y#$%;A/,#;\xe3/#$+\")(\"'#&'#06*%;A/,#;\xe3/#$+\")(\"'#&'#&/#$+\")(\"'#&'#"),$("%;\xea/k#;./b$;\xed/Y$$%;B/,#;\xe4/#$+\")(\"'#&'#06*%;B/,#;\xe4/#$+\")(\"'#&'#&/#$+$)($'#(#'#(\"'#&'#"),$(";\xe5.; &;\xe6.5 &;\xe7./ &;\xe8.) &;\xe9.# &;\xa0"),$("%3\u017c\"\"5#7\u017d/:#;</1$;\xf0/($8#:\u017e#! )(#'#(\"'#&'#"),$("%3\u017f\"\"5%7\u0180/:#;</1$;T/($8#:\u0181#! )(#'#(\"'#&'#"),$("%3\u0182\"\"5(7\u0183/F#;</=$;\\.) &;Y.# &;X/($8#:\u0184#! )(#'#(\"'#&'#"),$("%3\u0185\"\"5&7\u0186/:#;</1$;6/($8#:\u0187#! )(#'#(\"'#&'#"),$("%3\u0188\"\"5%7\u0189/A#;</8$$;!0#*;!&/($8#:\u018a#! )(#'#(\"'#&'#"),$("%;\xeb/G#;;/>$;6/5$;;/,$;\xec/#$+%)(%'#($'#(#'#(\"'#&'#"),$('%3\x92""5#7\xd3.# &;6/\' 8!:\u018b!! )'),$('%3\xb1""5#7\u018c.G &3\xb3""5#7\u018d.; &3\xb7""5#7\u018e./ &3\xb5""5$7\u018f.# &;6/\' 8!:\u0190!! )'),$('%;\xee/D#%;C/,#;\xef/#$+")("\'#&\'#." &"/#$+")("\'#&\'#'),$("%;U.) &;\\.# &;X/& 8!:\u0191! )"),$('%%;!." &"/[#;!." &"/M$;!." &"/?$;!." &"/1$;!." &"/#$+%)(%\'#($\'#(#\'#("\'#&\'#/\' 8!:\u0192!! )'),$('%%;!/?#;!." &"/1$;!." &"/#$+#)(#\'#("\'#&\'#/\' 8!:\u0193!! )'),$(";\xbe"),$('%;\x9e/^#$%;B/,#;\xf3/#$+")("\'#&\'#06*%;B/,#;\xf3/#$+")("\'#&\'#&/($8":\u0194"!!)("\'#&\'#'),$(";\xf4.# &;\xa0"),$('%2\u0195""6\u01957\u0196/L#;</C$2\u0197""6\u01977\u0198.) &2\u0199""6\u01997\u019a/($8#:\u019b#! )(#\'#("\'#&\'#'),$('%;\x9e/^#$%;B/,#;\xa0/#$+")("\'#&\'#06*%;B/,#;\xa0/#$+")("\'#&\'#&/($8":\u019c"!!)("\'#&\'#'),$("%;6/5#;0/,$;\xf7/#$+#)(#'#(\"'#&'#"),$("$;2.) &;4.# &;.0/*;2.) &;4.# &;.&"),$("$;%0#*;%&"),$("%;\xfa/;#28\"\"6879/,$;\xfb/#$+#)(#'#(\"'#&'#"),$('%3\u019d""5%7\u019e.) &3\u019f""5$7\u01a0/\' 8!:\u01a1!! )'),$('%;\xfc/J#%28""6879/,#;^/#$+")("\'#&\'#." &"/#$+")("\'#&\'#'),$("%;\\.) &;X.# &;\x82/' 8!:\u01a2!! )"),$(';".S &;!.M &2F""6F7G.A &2J""6J7K.5 &2H""6H7I.) &2N""6N7O'),$('2L""6L7M.\x95 &2B""6B7C.\x89 &2<""6<7=.} &2R""6R7S.q &2T""6T7U.e &2V""6V7W.Y &2P""6P7Q.M &2@""6@7A.A &2D""6D7E.5 &22""6273.) &2>""6>7?'),$('%;\u0100/b#28""6879/S$;\xfb/J$%2\u01a3""6\u01a37\u01a4/,#;\xec/#$+")("\'#&\'#." &"/#$+$)($\'#(#\'#("\'#&\'#'),$('%3\u01a5""5%7\u01a6.) &3\u01a7""5$7\u01a8/\' 8!:\u01a1!! )'),$('%3\xb1""5#7\xb2.6 &3\xb3""5#7\xb4.* &$;+0#*;+&/\' 8!:\u01a9!! )'),$("%;\u0104/\x87#2F\"\"6F7G/x$;\u0103/o$2F\"\"6F7G/`$;\u0103/W$2F\"\"6F7G/H$;\u0103/?$2F\"\"6F7G/0$;\u0105/'$8):\u01aa) )()'#(('#(''#(&'#(%'#($'#(#'#(\"'#&'#"),$("%;#/>#;#/5$;#/,$;#/#$+$)($'#(#'#(\"'#&'#"),$("%;\u0103/,#;\u0103/#$+\")(\"'#&'#"),$("%;\u0103/5#;\u0103/,$;\u0103/#$+#)(#'#(\"'#&'#"),$("%;q/T#$;m0#*;m&/D$%; /,#;\xf8/#$+\")(\"'#&'#.\" &\"/#$+#)(#'#(\"'#&'#"),$('%2\u01ab""6\u01ab7\u01ac.) &2\u01ad""6\u01ad7\u01ae/w#;0/n$;\u0108/e$$%;B/2#;\u0109.# &;\xa0/#$+")("\'#&\'#0<*%;B/2#;\u0109.# &;\xa0/#$+")("\'#&\'#&/#$+$)($\'#(#\'#("\'#&\'#'),$(";\x99.# &;L"),$("%2\u01af\"\"6\u01af7\u01b0/5#;</,$;\u010a/#$+#)(#'#(\"'#&'#"),$("%;D/S#;,/J$2:\"\"6:7;/;$;,.# &;T/,$;E/#$+%)(%'#($'#(#'#(\"'#&'#")];let a=0,c=0;const h=[{line:1,column:1}];let d,l=0,g=[],u=0;if(void 0!==t.startRule){if(!(t.startRule in i))throw new Error("Can't start parsing from rule \""+t.startRule+'".');r=i[t.startRule]}function p(){return e.substring(c,a)}function f(){return S(c,a)}function w(e,t){return{type:"literal",text:e,ignoreCase:t}}function T(e,t,s){return{type:"class",parts:e,inverted:t,ignoreCase:s}}function y(t){let s,i=h[t];if(i)return i;for(s=t-1;!h[s];)s--;for(i=h[s],i={line:i.line,column:i.column};s<t;)10===e.charCodeAt(s)?(i.line++,i.column=1):i.column++,s++;return h[t]=i,i}function S(e,t){const s=y(e),i=y(t);return{start:{offset:e,line:s.line,column:s.column},end:{offset:t,line:i.line,column:i.column}}}function R(e){a<l||(a>l&&(l=a,g=[]),g.push(e))}function E(e,t,s){return new b(b.buildMessage(e,t),e,t,s)}function $(e){return e.split("").map((e=>e.charCodeAt(0)-32))}if(t.data={},d=function t(i){const r=o[i];let h=0;const d=[];let l=r.length;const g=[],p=[];let f;for(;;){for(;h<l;)switch(r[h]){case 0:p.push(n[r[h+1]]),h+=2;break;case 1:p.push(void 0),h++;break;case 2:p.push(null),h++;break;case 3:p.push(s),h++;break;case 4:p.push([]),h++;break;case 5:p.push(a),h++;break;case 6:p.pop(),h++;break;case 7:a=p.pop(),h++;break;case 8:p.length-=r[h+1],h+=2;break;case 9:p.splice(-2,1),h++;break;case 10:p[p.length-2].push(p.pop()),h++;break;case 11:p.push(p.splice(p.length-r[h+1],r[h+1])),h+=2;break;case 12:p.push(e.substring(p.pop(),a)),h++;break;case 13:g.push(l),d.push(h+3+r[h+1]+r[h+2]),p[p.length-1]?(l=h+3+r[h+1],h+=3):(l=h+3+r[h+1]+r[h+2],h+=3+r[h+1]);break;case 14:g.push(l),d.push(h+3+r[h+1]+r[h+2]),p[p.length-1]===s?(l=h+3+r[h+1],h+=3):(l=h+3+r[h+1]+r[h+2],h+=3+r[h+1]);break;case 15:g.push(l),d.push(h+3+r[h+1]+r[h+2]),p[p.length-1]!==s?(l=h+3+r[h+1],h+=3):(l=h+3+r[h+1]+r[h+2],h+=3+r[h+1]);break;case 16:p[p.length-1]!==s?(g.push(l),d.push(h),l=h+2+r[h+1],h+=2):h+=2+r[h+1];break;case 17:g.push(l),d.push(h+3+r[h+1]+r[h+2]),e.length>a?(l=h+3+r[h+1],h+=3):(l=h+3+r[h+1]+r[h+2],h+=3+r[h+1]);break;case 18:g.push(l),d.push(h+4+r[h+2]+r[h+3]),e.substr(a,n[r[h+1]].length)===n[r[h+1]]?(l=h+4+r[h+2],h+=4):(l=h+4+r[h+2]+r[h+3],h+=4+r[h+2]);break;case 19:g.push(l),d.push(h+4+r[h+2]+r[h+3]),e.substr(a,n[r[h+1]].length).toLowerCase()===n[r[h+1]]?(l=h+4+r[h+2],h+=4):(l=h+4+r[h+2]+r[h+3],h+=4+r[h+2]);break;case 20:g.push(l),d.push(h+4+r[h+2]+r[h+3]),n[r[h+1]].test(e.charAt(a))?(l=h+4+r[h+2],h+=4):(l=h+4+r[h+2]+r[h+3],h+=4+r[h+2]);break;case 21:p.push(e.substr(a,r[h+1])),a+=r[h+1],h+=2;break;case 22:p.push(n[r[h+1]]),a+=n[r[h+1]].length,h+=2;break;case 23:p.push(s),0===u&&R(n[r[h+1]]),h+=2;break;case 24:c=p[p.length-1-r[h+1]],h+=2;break;case 25:c=a,h++;break;case 26:f=r.slice(h+4,h+4+r[h+3]).map((function(e){return p[p.length-1-e]})),p.splice(p.length-r[h+2],r[h+2],n[r[h+1]].apply(null,f)),h+=4+r[h+3];break;case 27:p.push(t(r[h+1])),h+=2;break;case 28:u++,h++;break;case 29:u--,h++;break;default:throw new Error("Invalid opcode: "+r[h]+".")}if(!(g.length>0))break;l=g.pop(),h=d.pop()}return p[0]}(r),d!==s&&a===e.length)return d;throw d!==s&&a<e.length&&R({type:"end"}),E(g,l<e.length?e.charAt(l):null,l<e.length?S(l,l+1):S(l,l))};var y;!function(e){e.parse=function(e,t){const s={startRule:t};try{T(e,s)}catch(e){s.data=-1}return s.data},e.nameAddrHeaderParse=function(t){const s=e.parse(t,"Name_Addr_Header");return-1!==s?s:void 0},e.URIParse=function(t){const s=e.parse(t,"SIP_URI");return-1!==s?s:void 0}}(y||(y={}));const S={100:"Trying",180:"Ringing",181:"Call Is Being Forwarded",182:"Queued",183:"Session Progress",199:"Early Dialog Terminated",200:"OK",202:"Accepted",204:"No Notification",300:"Multiple Choices",301:"Moved Permanently",302:"Moved Temporarily",305:"Use Proxy",380:"Alternative Service",400:"Bad Request",401:"Unauthorized",402:"Payment Required",403:"Forbidden",404:"Not Found",405:"Method Not Allowed",406:"Not Acceptable",407:"Proxy Authentication Required",408:"Request Timeout",410:"Gone",412:"Conditional Request Failed",413:"Request Entity Too Large",414:"Request-URI Too Long",415:"Unsupported Media Type",416:"Unsupported URI Scheme",417:"Unknown Resource-Priority",420:"Bad Extension",421:"Extension Required",422:"Session Interval Too Small",423:"Interval Too Brief",428:"Use Identity Header",429:"Provide Referrer Identity",430:"Flow Failed",433:"Anonymity Disallowed",436:"Bad Identity-Info",437:"Unsupported Certificate",438:"Invalid Identity Header",439:"First Hop Lacks Outbound Support",440:"Max-Breadth Exceeded",469:"Bad Info Package",470:"Consent Needed",478:"Unresolvable Destination",480:"Temporarily Unavailable",481:"Call/Transaction Does Not Exist",482:"Loop Detected",483:"Too Many Hops",484:"Address Incomplete",485:"Ambiguous",486:"Busy Here",487:"Request Terminated",488:"Not Acceptable Here",489:"Bad Event",491:"Request Pending",493:"Undecipherable",494:"Security Agreement Required",500:"Internal Server Error",501:"Not Implemented",502:"Bad Gateway",503:"Service Unavailable",504:"Server Time-out",505:"Version Not Supported",513:"Message Too Large",580:"Precondition Failure",600:"Busy Everywhere",603:"Decline",604:"Does Not Exist Anywhere",606:"Not Acceptable"};function R(e,t=32){let s="";for(let i=0;i<e;i++){s+=Math.floor(Math.random()*t).toString(t)}return s}function E(e){return S[e]||""}function $(){return R(10)}function I(e){const t={"Call-Id":"Call-ID",Cseq:"CSeq","Min-Se":"Min-SE",Rack:"RAck",Rseq:"RSeq","Www-Authenticate":"WWW-Authenticate"},s=e.toLowerCase().replace(/_/g,"-").split("-"),i=s.length;let r="";for(let e=0;e<i;e++)0!==e&&(r+="-"),r+=s[e].charAt(0).toUpperCase()+s[e].substring(1);return t[r]&&(r=t[r]),r}function C(e){return encodeURIComponent(e).replace(/%[A-F\d]{2}/g,"U").length}class A{constructor(){this.headers={}}addHeader(e,t){const s={raw:t};e=I(e),this.headers[e]?this.headers[e].push(s):this.headers[e]=[s]}getHeader(e){const t=this.headers[I(e)];if(t)return t[0]?t[0].raw:void 0}getHeaders(e){const t=this.headers[I(e)],s=[];if(!t)return[];for(const e of t)s.push(e.raw);return s}hasHeader(e){return!!this.headers[I(e)]}parseHeader(e,t=0){if(e=I(e),!this.headers[e])return;if(t>=this.headers[e].length)return;const s=this.headers[e][t],i=s.raw;if(s.parsed)return s.parsed;const r=y.parse(i,e.replace(/-/g,"_"));return-1===r?void this.headers[e].splice(t,1):(s.parsed=r,r)}s(e,t=0){return this.parseHeader(e,t)}setHeader(e,t){this.headers[I(e)]=[{raw:t}]}toString(){return this.data}}class D extends A{constructor(){super()}}class H extends A{constructor(){super()}}class k{constructor(e,t,s,i,r,n,o){this.headers={},this.extraHeaders=[],this.options=k.getDefaultOptions(),r&&(this.options=Object.assign(Object.assign({},this.options),r),this.options.optionTags&&this.options.optionTags.length&&(this.options.optionTags=this.options.optionTags.slice()),this.options.routeSet&&this.options.routeSet.length&&(this.options.routeSet=this.options.routeSet.slice())),n&&n.length&&(this.extraHeaders=n.slice()),o&&(this.body={body:o.content,contentType:o.contentType}),this.method=e,this.ruri=t.clone(),this.fromURI=s.clone(),this.fromTag=this.options.fromTag?this.options.fromTag:$(),this.from=k.makeNameAddrHeader(this.fromURI,this.options.fromDisplayName,this.fromTag),this.toURI=i.clone(),this.toTag=this.options.toTag,this.to=k.makeNameAddrHeader(this.toURI,this.options.toDisplayName,this.toTag),this.callId=this.options.callId?this.options.callId:this.options.callIdPrefix+R(15),this.cseq=this.options.cseq,this.setHeader("route",this.options.routeSet),this.setHeader("via",""),this.setHeader("to",this.to.toString()),this.setHeader("from",this.from.toString()),this.setHeader("cseq",this.cseq+" "+this.method),this.setHeader("call-id",this.callId),this.setHeader("max-forwards","70")}static getDefaultOptions(){return{callId:"",callIdPrefix:"",cseq:1,toDisplayName:"",toTag:"",fromDisplayName:"",fromTag:"",forceRport:!1,hackViaTcp:!1,optionTags:["outbound"],routeSet:[],userAgentString:"sip.js",viaHost:""}}static makeNameAddrHeader(e,t,s){const i={};return s&&(i.tag=s),new m(e,t,i)}getHeader(e){const t=this.headers[I(e)];if(t){if(t[0])return t[0]}else{const t=new RegExp("^\\s*"+e+"\\s*:","i");for(const e of this.extraHeaders)if(t.test(e))return e.substring(e.indexOf(":")+1).trim()}}getHeaders(e){const t=[],s=this.headers[I(e)];if(s)for(const e of s)t.push(e);else{const s=new RegExp("^\\s*"+e+"\\s*:","i");for(const e of this.extraHeaders)s.test(e)&&t.push(e.substring(e.indexOf(":")+1).trim())}return t}hasHeader(e){if(this.headers[I(e)])return!0;{const t=new RegExp("^\\s*"+e+"\\s*:","i");for(const e of this.extraHeaders)if(t.test(e))return!0}return!1}setHeader(e,t){this.headers[I(e)]=t instanceof Array?t:[t]}setViaHeader(e,t){this.options.hackViaTcp&&(t="TCP");let s="SIP/2.0/"+t;s+=" "+this.options.viaHost+";branch="+e,this.options.forceRport&&(s+=";rport"),this.setHeader("via",s),this.branch=e}toString(){let e="";e+=this.method+" "+this.ruri.toRaw()+" SIP/2.0\r\n";for(const t in this.headers)if(this.headers[t])for(const s of this.headers[t])e+=t+": "+s+"\r\n";for(const t of this.extraHeaders)e+=t.trim()+"\r\n";return e+="Supported: "+this.options.optionTags.join(", ")+"\r\n",e+="User-Agent: "+this.options.userAgentString+"\r\n",this.body?"string"==typeof this.body?(e+="Content-Length: "+C(this.body)+"\r\n\r\n",e+=this.body):this.body.body&&this.body.contentType?(e+="Content-Type: "+this.body.contentType+"\r\n",e+="Content-Length: "+C(this.body.body)+"\r\n\r\n",e+=this.body.body):e+="Content-Length: 0\r\n\r\n":e+="Content-Length: 0\r\n\r\n",e}}function _(e){return"application/sdp"===e?"session":"render"}function P(e){const t="string"==typeof e?e:e.body,s="string"==typeof e?"application/sdp":e.contentType;return{contentDisposition:_(s),contentType:s,content:t}}function q(e){return!(!e||"string"!=typeof e.content||"string"!=typeof e.contentType||void 0!==e.contentDisposition)||"string"==typeof e.contentDisposition}function x(e){let t,s,i;if(e instanceof D&&e.body){const r=e.parseHeader("Content-Disposition");t=r?r.type:void 0,s=e.parseHeader("Content-Type"),i=e.body}if(e instanceof H&&e.body){const r=e.parseHeader("Content-Disposition");t=r?r.type:void 0,s=e.parseHeader("Content-Type"),i=e.body}if(e instanceof k&&e.body)if(t=e.getHeader("Content-Disposition"),s=e.getHeader("Content-Type"),"string"==typeof e.body){if(!s)throw new Error("Header content type header does not equal body content type.");i=e.body}else{if(s&&s!==e.body.contentType)throw new Error("Header content type header does not equal body content type.");s=e.body.contentType,i=e.body.body}if(q(e)&&(t=e.contentDisposition,s=e.contentType,i=e.content),i){if(s&&!t&&(t=_(s)),!t)throw new Error("Content disposition undefined.");if(!s)throw new Error("Content type undefined.");return{contentDisposition:t,contentType:s,content:i}}}var N,M;!function(e){e.Initial="Initial",e.Early="Early",e.AckWait="AckWait",e.Confirmed="Confirmed",e.Terminated="Terminated"}(N||(N={})),function(e){e.Initial="Initial",e.HaveLocalOffer="HaveLocalOffer",e.HaveRemoteOffer="HaveRemoteOffer",e.Stable="Stable",e.Closed="Closed"}(M||(M={}));class O extends n{constructor(e){super(e||"Transaction state error.")}}const U=500,j=5e3,F={T1:U,T2:4e3,T4:j,TIMER_B:32e3,TIMER_D:0,TIMER_F:32e3,TIMER_H:32e3,TIMER_I:0,TIMER_J:0,TIMER_K:0,TIMER_L:32e3,TIMER_M:32e3,TIMER_N:32e3,PROVISIONAL_RESPONSE_INTERVAL:6e4};var L;!function(e){e.ACK="ACK",e.BYE="BYE",e.CANCEL="CANCEL",e.INFO="INFO",e.INVITE="INVITE",e.MESSAGE="MESSAGE",e.NOTIFY="NOTIFY",e.OPTIONS="OPTIONS",e.REGISTER="REGISTER",e.UPDATE="UPDATE",e.SUBSCRIBE="SUBSCRIBE",e.PUBLISH="PUBLISH",e.REFER="REFER",e.PRACK="PRACK"}(L||(L={}));const B=[L.ACK,L.BYE,L.CANCEL,L.INFO,L.INVITE,L.MESSAGE,L.NOTIFY,L.OPTIONS,L.PRACK,L.REFER,L.REGISTER,L.SUBSCRIBE];class G{constructor(e){this.incomingMessageRequest=e}get request(){return this.incomingMessageRequest.message}accept(e){return this.incomingMessageRequest.accept(e),Promise.resolve()}reject(e){return this.incomingMessageRequest.reject(e),Promise.resolve()}}class V{constructor(e){this.incomingNotifyRequest=e}get request(){return this.incomingNotifyRequest.message}accept(e){return this.incomingNotifyRequest.accept(e),Promise.resolve()}reject(e){return this.incomingNotifyRequest.reject(e),Promise.resolve()}}class K{constructor(e,t){this.incomingReferRequest=e,this.session=t}get referTo(){const e=this.incomingReferRequest.message.parseHeader("refer-to");if(!(e instanceof m))throw new Error("Failed to parse Refer-To header.");return e}get referredBy(){return this.incomingReferRequest.message.getHeader("referred-by")}get replaces(){const e=this.referTo.uri.getHeader("replaces");return e instanceof Array?e[0]:e}get request(){return this.incomingReferRequest.message}accept(e={statusCode:202}){return this.incomingReferRequest.accept(e),Promise.resolve()}reject(e){return this.incomingReferRequest.reject(e),Promise.resolve()}makeInviter(e){if(this.inviter)return this.inviter;const t=this.referTo.uri.clone();t.clearHeaders();const s=((e=e||{}).extraHeaders||[]).slice(),i=this.replaces;i&&s.push("Replaces: "+decodeURIComponent(i));const r=this.referredBy;return r&&s.push("Referred-By: "+r),e.extraHeaders=s,this.inviter=this.session.userAgent._makeInviter(t,e),this.inviter._referred=this.session,this.session._referral=this.inviter,this.inviter}}var W,Y;!function(e){e.Initial="Initial",e.Establishing="Establishing",e.Established="Established",e.Terminating="Terminating",e.Terminated="Terminated"}(W||(W={}));class Z{constructor(e,t={}){this.pendingReinvite=!1,this.pendingReinviteAck=!1,this._state=W.Initial,this.delegate=t.delegate,this._stateEventEmitter=new u,this._userAgent=e}dispose(){switch(this.logger.log(`Session ${this.id} in state ${this._state} is being disposed`),delete this.userAgent._sessions[this.id],this._sessionDescriptionHandler&&this._sessionDescriptionHandler.close(),this.state){case W.Initial:case W.Establishing:break;case W.Established:return new Promise((e=>{this._bye({onAccept:()=>e(),onRedirect:()=>e(),onReject:()=>e()})}));case W.Terminating:case W.Terminated:break;default:throw new Error("Unknown state.")}return Promise.resolve()}get assertedIdentity(){return this._assertedIdentity}get dialog(){return this._dialog}get id(){return this._id}get replacee(){return this._replacee}get sessionDescriptionHandler(){return this._sessionDescriptionHandler}get sessionDescriptionHandlerFactory(){return this.userAgent.configuration.sessionDescriptionHandlerFactory}get sessionDescriptionHandlerModifiers(){return this._sessionDescriptionHandlerModifiers||[]}set sessionDescriptionHandlerModifiers(e){this._sessionDescriptionHandlerModifiers=e.slice()}get sessionDescriptionHandlerOptions(){return this._sessionDescriptionHandlerOptions||{}}set sessionDescriptionHandlerOptions(e){this._sessionDescriptionHandlerOptions=Object.assign({},e)}get sessionDescriptionHandlerModifiersReInvite(){return this._sessionDescriptionHandlerModifiersReInvite||[]}set sessionDescriptionHandlerModifiersReInvite(e){this._sessionDescriptionHandlerModifiersReInvite=e.slice()}get sessionDescriptionHandlerOptionsReInvite(){return this._sessionDescriptionHandlerOptionsReInvite||{}}set sessionDescriptionHandlerOptionsReInvite(e){this._sessionDescriptionHandlerOptionsReInvite=Object.assign({},e)}get state(){return this._state}get stateChange(){return this._stateEventEmitter}get userAgent(){return this._userAgent}bye(e={}){let t="Session.bye() may only be called if established session.";switch(this.state){case W.Initial:"function"==typeof this.cancel?(t+=" However Inviter.invite() has not yet been called.",t+=" Perhaps you should have called Inviter.cancel()?"):"function"==typeof this.reject&&(t+=" However Invitation.accept() has not yet been called.",t+=" Perhaps you should have called Invitation.reject()?");break;case W.Establishing:"function"==typeof this.cancel?(t+=" However a dialog does not yet exist.",t+=" Perhaps you should have called Inviter.cancel()?"):"function"==typeof this.reject&&(t+=" However Invitation.accept() has not yet been called (or not yet resolved).",t+=" Perhaps you should have called Invitation.reject()?");break;case W.Established:{const t=e.requestDelegate,s=this.copyRequestOptions(e.requestOptions);return this._bye(t,s)}case W.Terminating:t+=" However this session is already terminating.","function"==typeof this.cancel?t+=" Perhaps you have already called Inviter.cancel()?":"function"==typeof this.reject&&(t+=" Perhaps you have already called Session.bye()?");break;case W.Terminated:t+=" However this session is already terminated.";break;default:throw new Error("Unknown state")}return this.logger.error(t),Promise.reject(new Error(`Invalid session state ${this.state}`))}info(e={}){if(this.state!==W.Established){const e="Session.info() may only be called if established session.";return this.logger.error(e),Promise.reject(new Error(`Invalid session state ${this.state}`))}const t=e.requestDelegate,s=this.copyRequestOptions(e.requestOptions);return this._info(t,s)}invite(e={}){if(this.logger.log("Session.invite"),this.state!==W.Established)return Promise.reject(new Error(`Invalid session state ${this.state}`));if(this.pendingReinvite)return Promise.reject(new a("Reinvite in progress. Please wait until complete, then try again."));this.pendingReinvite=!0,e.sessionDescriptionHandlerModifiers&&(this.sessionDescriptionHandlerModifiersReInvite=e.sessionDescriptionHandlerModifiers),e.sessionDescriptionHandlerOptions&&(this.sessionDescriptionHandlerOptionsReInvite=e.sessionDescriptionHandlerOptions);const t={onAccept:t=>{const s=x(t.message);if(!s)return this.logger.error("Received 2xx response to re-INVITE without a session description"),this.ackAndBye(t,400,"Missing session description"),this.stateTransition(W.Terminated),void(this.pendingReinvite=!1);if(e.withoutSdp){const i={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptionsReInvite,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiersReInvite};this.setOfferAndGetAnswer(s,i).then((e=>{t.ack({body:e})})).catch((e=>{this.logger.error("Failed to handle offer in 2xx response to re-INVITE"),this.logger.error(e.message),this.state===W.Terminated?t.ack():(this.ackAndBye(t,488,"Bad Media Description"),this.stateTransition(W.Terminated))})).then((()=>{this.pendingReinvite=!1,e.requestDelegate&&e.requestDelegate.onAccept&&e.requestDelegate.onAccept(t)}))}else{const i={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptionsReInvite,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiersReInvite};this.setAnswer(s,i).then((()=>{t.ack()})).catch((e=>{this.logger.error("Failed to handle answer in 2xx response to re-INVITE"),this.logger.error(e.message),this.state!==W.Terminated?(this.ackAndBye(t,488,"Bad Media Description"),this.stateTransition(W.Terminated)):t.ack()})).then((()=>{this.pendingReinvite=!1,e.requestDelegate&&e.requestDelegate.onAccept&&e.requestDelegate.onAccept(t)}))}},onProgress:e=>{},onRedirect:e=>{},onReject:t=>{this.logger.warn("Received a non-2xx response to re-INVITE"),this.pendingReinvite=!1,e.withoutSdp?e.requestDelegate&&e.requestDelegate.onReject&&e.requestDelegate.onReject(t):this.rollbackOffer().catch((e=>{if(this.logger.error("Failed to rollback offer on non-2xx response to re-INVITE"),this.logger.error(e.message),this.state!==W.Terminated){if(!this.dialog)throw new Error("Dialog undefined.");const e=[];e.push("Reason: "+this.getReasonHeaderValue(500,"Internal Server Error")),this.dialog.bye(void 0,{extraHeaders:e}),this.stateTransition(W.Terminated)}})).then((()=>{e.requestDelegate&&e.requestDelegate.onReject&&e.requestDelegate.onReject(t)}))},onTrying:e=>{}},s=e.requestOptions||{};if(s.extraHeaders=(s.extraHeaders||[]).slice(),s.extraHeaders.push("Allow: "+B.toString()),s.extraHeaders.push("Contact: "+this._contact),e.withoutSdp){if(!this.dialog)throw this.pendingReinvite=!1,new Error("Dialog undefined.");return Promise.resolve(this.dialog.invite(t,s))}const i={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptionsReInvite,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiersReInvite};return this.getOffer(i).then((e=>{if(!this.dialog)throw this.pendingReinvite=!1,new Error("Dialog undefined.");return s.body=e,this.dialog.invite(t,s)})).catch((e=>{throw this.logger.error(e.message),this.logger.error("Failed to send re-INVITE"),this.pendingReinvite=!1,e}))}message(e={}){if(this.state!==W.Established){const e="Session.message() may only be called if established session.";return this.logger.error(e),Promise.reject(new Error(`Invalid session state ${this.state}`))}const t=e.requestDelegate,s=this.copyRequestOptions(e.requestOptions);return this._message(t,s)}refer(e,t={}){if(this.state!==W.Established){const e="Session.refer() may only be called if established session.";return this.logger.error(e),Promise.reject(new Error(`Invalid session state ${this.state}`))}const s=t.requestDelegate,i=this.copyRequestOptions(t.requestOptions);return i.extraHeaders=i.extraHeaders?i.extraHeaders.concat(this.referExtraHeaders(this.referToString(e))):this.referExtraHeaders(this.referToString(e)),this._refer(t.onNotify,s,i)}_bye(e,t){if(!this.dialog)return Promise.reject(new Error("Session dialog undefined."));const s=this.dialog;switch(s.sessionState){case N.Initial:case N.Early:throw new Error(`Invalid dialog state ${s.sessionState}`);case N.AckWait:return this.stateTransition(W.Terminating),new Promise((i=>{s.delegate={onAck:()=>{const r=s.bye(e,t);return this.stateTransition(W.Terminated),i(r),Promise.resolve()},onAckTimeout:()=>{const r=s.bye(e,t);this.stateTransition(W.Terminated),i(r)}}}));case N.Confirmed:{const i=s.bye(e,t);return this.stateTransition(W.Terminated),Promise.resolve(i)}case N.Terminated:throw new Error(`Invalid dialog state ${s.sessionState}`);default:throw new Error("Unrecognized state.")}}_info(e,t){return this.dialog?Promise.resolve(this.dialog.info(e,t)):Promise.reject(new Error("Session dialog undefined."))}_message(e,t){return this.dialog?Promise.resolve(this.dialog.message(e,t)):Promise.reject(new Error("Session dialog undefined."))}_refer(e,t,s){return this.dialog?(this.onNotify=e,Promise.resolve(this.dialog.refer(t,s))):Promise.reject(new Error("Session dialog undefined."))}ackAndBye(e,t,s){e.ack();const i=[];t&&i.push("Reason: "+this.getReasonHeaderValue(t,s)),e.session.bye(void 0,{extraHeaders:i})}onAckRequest(e){if(this.logger.log("Session.onAckRequest"),this.state!==W.Established&&this.state!==W.Terminating)return this.logger.error(`ACK received while in state ${this.state}, dropping request`),Promise.resolve();const t=this.dialog;if(!t)throw new Error("Dialog undefined.");const s={sessionDescriptionHandlerOptions:this.pendingReinviteAck?this.sessionDescriptionHandlerOptionsReInvite:this.sessionDescriptionHandlerOptions,sessionDescriptionHandlerModifiers:this.pendingReinviteAck?this._sessionDescriptionHandlerModifiersReInvite:this._sessionDescriptionHandlerModifiers};if(this.delegate&&this.delegate.onAck){const t=new l(e);this.delegate.onAck(t)}switch(this.pendingReinviteAck=!1,t.signalingState){case M.Initial:{this.logger.error(`Invalid signaling state ${t.signalingState}.`);const e=["Reason: "+this.getReasonHeaderValue(488,"Bad Media Description")];return t.bye(void 0,{extraHeaders:e}),this.stateTransition(W.Terminated),Promise.resolve()}case M.Stable:{const i=x(e.message);return i?"render"===i.contentDisposition?(this._renderbody=i.content,this._rendertype=i.contentType,Promise.resolve()):"session"!==i.contentDisposition?Promise.resolve():this.setAnswer(i,s).catch((e=>{this.logger.error(e.message);const s=["Reason: "+this.getReasonHeaderValue(488,"Bad Media Description")];t.bye(void 0,{extraHeaders:s}),this.stateTransition(W.Terminated)})):Promise.resolve()}case M.HaveLocalOffer:{this.logger.error(`Invalid signaling state ${t.signalingState}.`);const e=["Reason: "+this.getReasonHeaderValue(488,"Bad Media Description")];return t.bye(void 0,{extraHeaders:e}),this.stateTransition(W.Terminated),Promise.resolve()}case M.HaveRemoteOffer:{this.logger.error(`Invalid signaling state ${t.signalingState}.`);const e=["Reason: "+this.getReasonHeaderValue(488,"Bad Media Description")];return t.bye(void 0,{extraHeaders:e}),this.stateTransition(W.Terminated),Promise.resolve()}case M.Closed:default:throw new Error(`Invalid signaling state ${t.signalingState}.`)}}onByeRequest(e){if(this.logger.log("Session.onByeRequest"),this.state===W.Established){if(this.delegate&&this.delegate.onBye){const t=new g(e);this.delegate.onBye(t)}else e.accept();this.stateTransition(W.Terminated)}else this.logger.error(`BYE received while in state ${this.state}, dropping request`)}onInfoRequest(e){if(this.logger.log("Session.onInfoRequest"),this.state===W.Established)if(this.delegate&&this.delegate.onInfo){const t=new p(e);this.delegate.onInfo(t)}else e.accept();else this.logger.error(`INFO received while in state ${this.state}, dropping request`)}onInviteRequest(e){if(this.logger.log("Session.onInviteRequest"),this.state!==W.Established)return void this.logger.error(`INVITE received while in state ${this.state}, dropping request`);this.pendingReinviteAck=!0;const t=["Contact: "+this._contact];if(e.message.hasHeader("P-Asserted-Identity")){const t=e.message.getHeader("P-Asserted-Identity");if(!t)throw new Error("Header undefined.");this._assertedIdentity=y.nameAddrHeaderParse(t)}const s={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptionsReInvite,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiersReInvite};this.generateResponseOfferAnswerInDialog(s).then((s=>{const i=e.accept({statusCode:200,extraHeaders:t,body:s});this.delegate&&this.delegate.onInvite&&this.delegate.onInvite(e.message,i.message,200)})).catch((s=>{if(this.logger.error(s.message),this.logger.error("Failed to handle to re-INVITE request"),!this.dialog)throw new Error("Dialog undefined.");if(this.logger.error(this.dialog.signalingState),this.dialog.signalingState!==M.Stable)this.rollbackOffer().then((()=>{const t=e.reject({statusCode:488});this.delegate&&this.delegate.onInvite&&this.delegate.onInvite(e.message,t.message,488)})).catch((s=>{this.logger.error(s.message),this.logger.error("Failed to rollback offer on re-INVITE request");const i=e.reject({statusCode:488});if(this.state!==W.Terminated){if(!this.dialog)throw new Error("Dialog undefined.");[].push("Reason: "+this.getReasonHeaderValue(500,"Internal Server Error")),this.dialog.bye(void 0,{extraHeaders:t}),this.stateTransition(W.Terminated)}this.delegate&&this.delegate.onInvite&&this.delegate.onInvite(e.message,i.message,488)}));else{const t=e.reject({statusCode:488});this.delegate&&this.delegate.onInvite&&this.delegate.onInvite(e.message,t.message,488)}}))}onMessageRequest(e){if(this.logger.log("Session.onMessageRequest"),this.state===W.Established)if(this.delegate&&this.delegate.onMessage){const t=new G(e);this.delegate.onMessage(t)}else e.accept();else this.logger.error(`MESSAGE received while in state ${this.state}, dropping request`)}onNotifyRequest(e){if(this.logger.log("Session.onNotifyRequest"),this.state===W.Established)if(this.onNotify){const t=new V(e);this.onNotify(t)}else if(this.delegate&&this.delegate.onNotify){const t=new V(e);this.delegate.onNotify(t)}else e.accept();else this.logger.error(`NOTIFY received while in state ${this.state}, dropping request`)}onPrackRequest(e){if(this.logger.log("Session.onPrackRequest"),this.state===W.Established)throw new Error("Unimplemented.");this.logger.error(`PRACK received while in state ${this.state}, dropping request`)}onReferRequest(e){if(this.logger.log("Session.onReferRequest"),this.state!==W.Established)return void this.logger.error(`REFER received while in state ${this.state}, dropping request`);if(!e.message.hasHeader("refer-to"))return this.logger.warn("Invalid REFER packet. A refer-to header is required. Rejecting."),void e.reject();const t=new K(e,this);this.delegate&&this.delegate.onRefer?this.delegate.onRefer(t):(this.logger.log("No delegate available to handle REFER, automatically accepting and following."),t.accept().then((()=>t.makeInviter(this._referralInviterOptions).invite())).catch((e=>{this.logger.error(e.message)})))}generateResponseOfferAnswer(e,t){if(this.dialog)return this.generateResponseOfferAnswerInDialog(t);const s=x(e.message);return s&&"session"===s.contentDisposition?this.setOfferAndGetAnswer(s,t):this.getOffer(t)}generateResponseOfferAnswerInDialog(e){if(!this.dialog)throw new Error("Dialog undefined.");switch(this.dialog.signalingState){case M.Initial:return this.getOffer(e);case M.HaveLocalOffer:return Promise.resolve(void 0);case M.HaveRemoteOffer:if(!this.dialog.offer)throw new Error(`Session offer undefined in signaling state ${this.dialog.signalingState}.`);return this.setOfferAndGetAnswer(this.dialog.offer,e);case M.Stable:return this.state!==W.Established?Promise.resolve(void 0):this.getOffer(e);case M.Closed:default:throw new Error(`Invalid signaling state ${this.dialog.signalingState}.`)}}getOffer(e){const t=this.setupSessionDescriptionHandler(),s=e.sessionDescriptionHandlerOptions,i=e.sessionDescriptionHandlerModifiers;try{return t.getDescription(s,i).then((e=>P(e))).catch((e=>{this.logger.error("Session.getOffer: SDH getDescription rejected...");const t=e instanceof Error?e:new Error("Session.getOffer unknown error.");throw this.logger.error(t.message),t}))}catch(e){this.logger.error("Session.getOffer: SDH getDescription threw...");const t=e instanceof Error?e:new Error(e);return this.logger.error(t.message),Promise.reject(t)}}rollbackOffer(){const e=this.setupSessionDescriptionHandler();if(void 0===e.rollbackDescription)return Promise.resolve();try{return e.rollbackDescription().catch((e=>{this.logger.error("Session.rollbackOffer: SDH rollbackDescription rejected...");const t=e instanceof Error?e:new Error("Session.rollbackOffer unknown error.");throw this.logger.error(t.message),t}))}catch(e){this.logger.error("Session.rollbackOffer: SDH rollbackDescription threw...");const t=e instanceof Error?e:new Error(e);return this.logger.error(t.message),Promise.reject(t)}}setAnswer(e,t){const s=this.setupSessionDescriptionHandler(),i=t.sessionDescriptionHandlerOptions,r=t.sessionDescriptionHandlerModifiers;try{if(!s.hasDescription(e.contentType))return Promise.reject(new o)}catch(e){this.logger.error("Session.setAnswer: SDH hasDescription threw...");const t=e instanceof Error?e:new Error(e);return this.logger.error(t.message),Promise.reject(t)}try{return s.setDescription(e.content,i,r).catch((e=>{this.logger.error("Session.setAnswer: SDH setDescription rejected...");const t=e instanceof Error?e:new Error("Session.setAnswer unknown error.");throw this.logger.error(t.message),t}))}catch(e){this.logger.error("Session.setAnswer: SDH setDescription threw...");const t=e instanceof Error?e:new Error(e);return this.logger.error(t.message),Promise.reject(t)}}setOfferAndGetAnswer(e,t){const s=this.setupSessionDescriptionHandler(),i=t.sessionDescriptionHandlerOptions,r=t.sessionDescriptionHandlerModifiers;try{if(!s.hasDescription(e.contentType))return Promise.reject(new o)}catch(e){this.logger.error("Session.setOfferAndGetAnswer: SDH hasDescription threw...");const t=e instanceof Error?e:new Error(e);return this.logger.error(t.message),Promise.reject(t)}try{return s.setDescription(e.content,i,r).then((()=>s.getDescription(i,r))).then((e=>P(e))).catch((e=>{this.logger.error("Session.setOfferAndGetAnswer: SDH setDescription or getDescription rejected...");const t=e instanceof Error?e:new Error("Session.setOfferAndGetAnswer unknown error.");throw this.logger.error(t.message),t}))}catch(e){this.logger.error("Session.setOfferAndGetAnswer: SDH setDescription or getDescription threw...");const t=e instanceof Error?e:new Error(e);return this.logger.error(t.message),Promise.reject(t)}}setSessionDescriptionHandler(e){if(this._sessionDescriptionHandler)throw new Error("Session description handler defined.");this._sessionDescriptionHandler=e}setupSessionDescriptionHandler(){var e;return this._sessionDescriptionHandler||(this._sessionDescriptionHandler=this.sessionDescriptionHandlerFactory(this,this.userAgent.configuration.sessionDescriptionHandlerFactoryOptions),(null===(e=this.delegate)||void 0===e?void 0:e.onSessionDescriptionHandler)&&this.delegate.onSessionDescriptionHandler(this._sessionDescriptionHandler,!1)),this._sessionDescriptionHandler}stateTransition(e){const t=()=>{throw new Error(`Invalid state transition from ${this._state} to ${e}`)};switch(this._state){case W.Initial:e!==W.Establishing&&e!==W.Established&&e!==W.Terminating&&e!==W.Terminated&&t();break;case W.Establishing:e!==W.Established&&e!==W.Terminating&&e!==W.Terminated&&t();break;case W.Established:e!==W.Terminating&&e!==W.Terminated&&t();break;case W.Terminating:e!==W.Terminated&&t();break;case W.Terminated:t();break;default:throw new Error("Unrecognized state.")}this._state=e,this.logger.log(`Session ${this.id} transitioned to state ${this._state}`),this._stateEventEmitter.emit(this._state),e===W.Terminated&&this.dispose()}copyRequestOptions(e={}){return{extraHeaders:e.extraHeaders?e.extraHeaders.slice():void 0,body:e.body?{contentDisposition:e.body.contentDisposition||"render",contentType:e.body.contentType||"text/plain",content:e.body.content||""}:void 0}}getReasonHeaderValue(e,t){const s=e;let i=E(e);return!i&&t&&(i=t),"SIP;cause="+s+';text="'+i+'"'}referExtraHeaders(e){const t=[];return t.push("Referred-By: <"+this.userAgent.configuration.uri+">"),t.push("Contact: "+this._contact),t.push("Allow: "+["ACK","CANCEL","INVITE","MESSAGE","BYE","OPTIONS","INFO","NOTIFY","REFER"].toString()),t.push("Refer-To: "+e),t}referToString(e){let t;if(e instanceof v)t=e.toString();else{if(!e.dialog)throw new Error("Dialog undefined.");const s=e.remoteIdentity.friendlyName,i=e.dialog.remoteTarget.toString(),r=e.dialog.callId,n=e.dialog.remoteTag,o=e.dialog.localTag;t=`"${s}" <${i}?Replaces=${encodeURIComponent(`${r};to-tag=${n};from-tag=${o}`)}>`}return t}}!function(e){e.Required="Required",e.Supported="Supported",e.Unsupported="Unsupported"}(Y||(Y={}));const J={"100rel":!0,199:!0,answermode:!0,"early-session":!0,eventlist:!0,explicitsub:!0,"from-change":!0,"geolocation-http":!0,"geolocation-sip":!0,gin:!0,gruu:!0,histinfo:!0,ice:!0,join:!0,"multiple-refer":!0,norefersub:!0,nosub:!0,outbound:!0,path:!0,policy:!0,precondition:!0,pref:!0,privacy:!0,"recipient-list-invite":!0,"recipient-list-message":!0,"recipient-list-subscribe":!0,replaces:!0,"resource-priority":!0,"sdp-anat":!0,"sec-agree":!0,tdialog:!0,timer:!0,uui:!0};class z extends Z{constructor(e,t){super(e),this.incomingInviteRequest=t,this.disposed=!1,this.expiresTimer=void 0,this.isCanceled=!1,this.rel100="none",this.rseq=Math.floor(1e4*Math.random()),this.userNoAnswerTimer=void 0,this.waitingForPrack=!1,this.logger=e.getLogger("sip.Invitation");const s=this.incomingInviteRequest.message,i=s.getHeader("require");i&&i.toLowerCase().includes("100rel")&&(this.rel100="required");const r=s.getHeader("supported");if(r&&r.toLowerCase().includes("100rel")&&(this.rel100="supported"),s.toTag=t.toTag,"string"!=typeof s.toTag)throw new TypeError("toTag should have been a string.");if(this.userNoAnswerTimer=setTimeout((()=>{t.reject({statusCode:480}),this.stateTransition(W.Terminated)}),this.userAgent.configuration.noAnswerTimeout?1e3*this.userAgent.configuration.noAnswerTimeout:6e4),s.hasHeader("expires")){const e=1e3*Number(s.getHeader("expires")||0);this.expiresTimer=setTimeout((()=>{this.state===W.Initial&&(t.reject({statusCode:487}),this.stateTransition(W.Terminated))}),e)}const n=this.request.getHeader("P-Asserted-Identity");n&&(this._assertedIdentity=y.nameAddrHeaderParse(n)),this._contact=this.userAgent.contact.toString();const o=s.parseHeader("Content-Disposition");o&&"render"===o.type&&(this._renderbody=s.body,this._rendertype=s.getHeader("Content-Type")),this._id=s.callId+s.fromTag,this.userAgent._sessions[this._id]=this}dispose(){if(this.disposed)return Promise.resolve();switch(this.disposed=!0,this.expiresTimer&&(clearTimeout(this.expiresTimer),this.expiresTimer=void 0),this.userNoAnswerTimer&&(clearTimeout(this.userNoAnswerTimer),this.userNoAnswerTimer=void 0),this.prackNeverArrived(),this.state){case W.Initial:case W.Establishing:return this.reject().then((()=>super.dispose()));case W.Established:case W.Terminating:case W.Terminated:return super.dispose();default:throw new Error("Unknown state.")}}get autoSendAnInitialProvisionalResponse(){return"required"!==this.rel100&&this.userAgent.configuration.sendInitialProvisionalResponse}get body(){return this.incomingInviteRequest.message.body}get localIdentity(){return this.request.to}get remoteIdentity(){return this.request.from}get request(){return this.incomingInviteRequest.message}accept(e={}){if(this.logger.log("Invitation.accept"),this.state!==W.Initial){const e=new Error(`Invalid session state ${this.state}`);return this.logger.error(e.message),Promise.reject(e)}return e.sessionDescriptionHandlerModifiers&&(this.sessionDescriptionHandlerModifiers=e.sessionDescriptionHandlerModifiers),e.sessionDescriptionHandlerOptions&&(this.sessionDescriptionHandlerOptions=e.sessionDescriptionHandlerOptions),this.stateTransition(W.Establishing),this.sendAccept(e).then((({message:e,session:t})=>{t.delegate={onAck:e=>this.onAckRequest(e),onAckTimeout:()=>this.onAckTimeout(),onBye:e=>this.onByeRequest(e),onInfo:e=>this.onInfoRequest(e),onInvite:e=>this.onInviteRequest(e),onMessage:e=>this.onMessageRequest(e),onNotify:e=>this.onNotifyRequest(e),onPrack:e=>this.onPrackRequest(e),onRefer:e=>this.onReferRequest(e)},this._dialog=t,this.stateTransition(W.Established),this._replacee&&this._replacee._bye()})).catch((e=>this.handleResponseError(e)))}progress(e={}){if(this.logger.log("Invitation.progress"),this.state!==W.Initial){const e=new Error(`Invalid session state ${this.state}`);return this.logger.error(e.message),Promise.reject(e)}const t=e.statusCode||180;if(t<100||t>199)throw new TypeError("Invalid statusCode: "+t);return e.sessionDescriptionHandlerModifiers&&(this.sessionDescriptionHandlerModifiers=e.sessionDescriptionHandlerModifiers),e.sessionDescriptionHandlerOptions&&(this.sessionDescriptionHandlerOptions=e.sessionDescriptionHandlerOptions),this.waitingForPrack?(this.logger.warn("Unexpected call for progress while waiting for prack, ignoring"),Promise.resolve()):100===e.statusCode?this.sendProgressTrying().then((()=>{})).catch((e=>this.handleResponseError(e))):"required"===this.rel100||"supported"===this.rel100&&e.rel100||"supported"===this.rel100&&this.userAgent.configuration.sipExtension100rel===Y.Required?this.sendProgressReliableWaitForPrack(e).then((()=>{})).catch((e=>this.handleResponseError(e))):this.sendProgress(e).then((()=>{})).catch((e=>this.handleResponseError(e)))}reject(e={}){if(this.logger.log("Invitation.reject"),this.state!==W.Initial&&this.state!==W.Establishing){const e=new Error(`Invalid session state ${this.state}`);return this.logger.error(e.message),Promise.reject(e)}const t=e.statusCode||480,s=e.reasonPhrase?e.reasonPhrase:E(t),i=e.extraHeaders||[];if(t<300||t>699)throw new TypeError("Invalid statusCode: "+t);const r=e.body?P(e.body):void 0;return t<400?this.incomingInviteRequest.redirect([],{statusCode:t,reasonPhrase:s,extraHeaders:i,body:r}):this.incomingInviteRequest.reject({statusCode:t,reasonPhrase:s,extraHeaders:i,body:r}),this.stateTransition(W.Terminated),Promise.resolve()}_onCancel(e){this.logger.log("Invitation._onCancel"),this.state===W.Initial||this.state===W.Establishing?(this.isCanceled=!0,this.incomingInviteRequest.reject({statusCode:487}),this.stateTransition(W.Terminated)):this.logger.error(`CANCEL received while in state ${this.state}, dropping request`)}handlePrackOfferAnswer(e){if(!this.dialog)throw new Error("Dialog undefined.");const t=x(e.message);if(!t||"session"!==t.contentDisposition)return Promise.resolve(void 0);const s={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers};switch(this.dialog.signalingState){case M.Initial:throw new Error(`Invalid signaling state ${this.dialog.signalingState}.`);case M.Stable:return this.setAnswer(t,s).then((()=>{}));case M.HaveLocalOffer:throw new Error(`Invalid signaling state ${this.dialog.signalingState}.`);case M.HaveRemoteOffer:return this.setOfferAndGetAnswer(t,s);case M.Closed:default:throw new Error(`Invalid signaling state ${this.dialog.signalingState}.`)}}handleResponseError(e){let t=480;if(e instanceof Error?this.logger.error(e.message):this.logger.error(e),e instanceof o?(this.logger.error("A session description handler occurred while sending response (content type unsupported"),t=415):e instanceof c?this.logger.error("A session description handler occurred while sending response"):e instanceof h?this.logger.error("Session ended before response could be formulated and sent (while waiting for PRACK)"):e instanceof O&&this.logger.error("Session changed state before response could be formulated and sent"),this.state===W.Initial||this.state===W.Establishing)try{this.incomingInviteRequest.reject({statusCode:t}),this.stateTransition(W.Terminated)}catch(e){throw this.logger.error("An error occurred attempting to reject the request while handling another error"),e}if(!this.isCanceled)throw e;this.logger.warn("An error occurred while attempting to formulate and send a response to an incoming INVITE. However a CANCEL was received and processed while doing so which can (and often does) result in errors occurring as the session terminates in the meantime. Said error is being ignored.")}onAckTimeout(){if(this.logger.log("Invitation.onAckTimeout"),!this.dialog)throw new Error("Dialog undefined.");this.logger.log("No ACK received for an extended period of time, terminating session"),this.dialog.bye(),this.stateTransition(W.Terminated)}sendAccept(e={}){const t={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers},s=e.extraHeaders||[];return this.waitingForPrack?this.waitForArrivalOfPrack().then((()=>clearTimeout(this.userNoAnswerTimer))).then((()=>this.generateResponseOfferAnswer(this.incomingInviteRequest,t))).then((e=>this.incomingInviteRequest.accept({statusCode:200,body:e,extraHeaders:s}))):(clearTimeout(this.userNoAnswerTimer),this.generateResponseOfferAnswer(this.incomingInviteRequest,t).then((e=>this.incomingInviteRequest.accept({statusCode:200,body:e,extraHeaders:s}))))}sendProgress(e={}){const t=e.statusCode||180,s=e.reasonPhrase,i=(e.extraHeaders||[]).slice(),r=e.body?P(e.body):void 0;if(183===t&&!r)return this.sendProgressWithSDP(e);try{const e=this.incomingInviteRequest.progress({statusCode:t,reasonPhrase:s,extraHeaders:i,body:r});return this._dialog=e.session,Promise.resolve(e)}catch(e){return Promise.reject(e)}}sendProgressWithSDP(e={}){const t={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers},s=e.statusCode||183,i=e.reasonPhrase,r=(e.extraHeaders||[]).slice();return this.generateResponseOfferAnswer(this.incomingInviteRequest,t).then((e=>this.incomingInviteRequest.progress({statusCode:s,reasonPhrase:i,extraHeaders:r,body:e}))).then((e=>(this._dialog=e.session,e)))}sendProgressReliable(e={}){return e.extraHeaders=(e.extraHeaders||[]).slice(),e.extraHeaders.push("Require: 100rel"),e.extraHeaders.push("RSeq: "+Math.floor(1e4*Math.random())),this.sendProgressWithSDP(e)}sendProgressReliableWaitForPrack(e={}){const t={sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions,sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers},s=e.statusCode||183,i=e.reasonPhrase,r=(e.extraHeaders||[]).slice();let n;return r.push("Require: 100rel"),r.push("RSeq: "+this.rseq++),new Promise(((e,o)=>{this.waitingForPrack=!0,this.generateResponseOfferAnswer(this.incomingInviteRequest,t).then((e=>(n=e,this.incomingInviteRequest.progress({statusCode:s,reasonPhrase:i,extraHeaders:r,body:n})))).then((t=>{let a,c;this._dialog=t.session,t.session.delegate={onPrack:s=>{a=s,clearTimeout(d),clearTimeout(u),this.waitingForPrack&&(this.waitingForPrack=!1,this.handlePrackOfferAnswer(a).then((s=>{try{c=a.accept({statusCode:200,body:s}),this.prackArrived(),e({prackRequest:a,prackResponse:c,progressResponse:t})}catch(e){o(e)}})).catch((e=>o(e))))}};const d=setTimeout((()=>{this.waitingForPrack&&(this.waitingForPrack=!1,this.logger.warn("No PRACK received, rejecting INVITE."),clearTimeout(u),this.reject({statusCode:504}).then((()=>o(new h))).catch((e=>o(e))))}),64*F.T1),l=()=>{try{this.incomingInviteRequest.progress({statusCode:s,reasonPhrase:i,extraHeaders:r,body:n})}catch(e){return this.waitingForPrack=!1,void o(e)}u=setTimeout(l,g*=2)};let g=F.T1,u=setTimeout(l,g)})).catch((e=>{this.waitingForPrack=!1,o(e)}))}))}sendProgressTrying(){try{const e=this.incomingInviteRequest.trying();return Promise.resolve(e)}catch(e){return Promise.reject(e)}}waitForArrivalOfPrack(){if(this.waitingForPrackPromise)throw new Error("Already waiting for PRACK");return this.waitingForPrackPromise=new Promise(((e,t)=>{this.waitingForPrackResolve=e,this.waitingForPrackReject=t})),this.waitingForPrackPromise}prackArrived(){this.waitingForPrackResolve&&this.waitingForPrackResolve(),this.waitingForPrackPromise=void 0,this.waitingForPrackResolve=void 0,this.waitingForPrackReject=void 0}prackNeverArrived(){this.waitingForPrackReject&&this.waitingForPrackReject(new h),this.waitingForPrackPromise=void 0,this.waitingForPrackResolve=void 0,this.waitingForPrackReject=void 0}}class X extends Z{constructor(e,t,s={}){super(e,s),this.disposed=!1,this.earlyMedia=!1,this.earlyMediaSessionDescriptionHandlers=new Map,this.isCanceled=!1,this.inviteWithoutSdp=!1,this.logger=e.getLogger("sip.Inviter"),this.earlyMedia=void 0!==s.earlyMedia?s.earlyMedia:this.earlyMedia,this.fromTag=$(),this.inviteWithoutSdp=void 0!==s.inviteWithoutSdp?s.inviteWithoutSdp:this.inviteWithoutSdp;const i=Object.assign({},s);i.params=Object.assign({},s.params);const r=s.anonymous||!1,n=e.contact.toString({anonymous:r,outbound:r?!e.contact.tempGruu:!e.contact.pubGruu});r&&e.configuration.uri&&(i.params.fromDisplayName="Anonymous",i.params.fromUri="sip:anonymous@anonymous.invalid");let o=e.userAgentCore.configuration.aor;if(i.params.fromUri&&(o="string"==typeof i.params.fromUri?y.URIParse(i.params.fromUri):i.params.fromUri),!o)throw new TypeError("Invalid from URI: "+i.params.fromUri);let a=t;if(i.params.toUri&&(a="string"==typeof i.params.toUri?y.URIParse(i.params.toUri):i.params.toUri),!a)throw new TypeError("Invalid to URI: "+i.params.toUri);const c=Object.assign({},i.params);c.fromTag=this.fromTag;const h=(i.extraHeaders||[]).slice();r&&e.configuration.uri&&(h.push("P-Preferred-Identity: "+e.configuration.uri.toString()),h.push("Privacy: id")),h.push("Contact: "+n),h.push("Allow: "+["ACK","CANCEL","INVITE","MESSAGE","BYE","OPTIONS","INFO","NOTIFY","REFER"].toString()),e.configuration.sipExtension100rel===Y.Required&&h.push("Require: 100rel"),e.configuration.sipExtensionReplaces===Y.Required&&h.push("Require: replaces"),i.extraHeaders=h;this.outgoingRequestMessage=e.userAgentCore.makeOutgoingRequestMessage(L.INVITE,t,o,a,c,h,undefined),this._contact=n,this._referralInviterOptions=i,this._renderbody=s.renderbody,this._rendertype=s.rendertype,s.sessionDescriptionHandlerModifiers&&(this.sessionDescriptionHandlerModifiers=s.sessionDescriptionHandlerModifiers),s.sessionDescriptionHandlerOptions&&(this.sessionDescriptionHandlerOptions=s.sessionDescriptionHandlerOptions),s.sessionDescriptionHandlerModifiersReInvite&&(this.sessionDescriptionHandlerModifiersReInvite=s.sessionDescriptionHandlerModifiersReInvite),s.sessionDescriptionHandlerOptionsReInvite&&(this.sessionDescriptionHandlerOptionsReInvite=s.sessionDescriptionHandlerOptionsReInvite),this._id=this.outgoingRequestMessage.callId+this.fromTag,this.userAgent._sessions[this._id]=this}dispose(){if(this.disposed)return Promise.resolve();switch(this.disposed=!0,this.disposeEarlyMedia(),this.state){case W.Initial:case W.Establishing:return this.cancel().then((()=>super.dispose()));case W.Established:case W.Terminating:case W.Terminated:return super.dispose();default:throw new Error("Unknown state.")}}get body(){return this.outgoingRequestMessage.body}get localIdentity(){return this.outgoingRequestMessage.from}get remoteIdentity(){return this.outgoingRequestMessage.to}get request(){return this.outgoingRequestMessage}cancel(e={}){if(this.logger.log("Inviter.cancel"),this.state!==W.Initial&&this.state!==W.Establishing){const e=new Error(`Invalid session state ${this.state}`);return this.logger.error(e.message),Promise.reject(e)}if(this.isCanceled=!0,this.stateTransition(W.Terminating),this.outgoingInviteRequest){let t;e.statusCode&&e.reasonPhrase&&(t=function(e,t){if(e&&e<200||e>699)throw new TypeError("Invalid statusCode: "+e);if(e)return"SIP;cause="+e+';text="'+(E(e)||t)+'"'}(e.statusCode,e.reasonPhrase)),this.outgoingInviteRequest.cancel(t,e)}else this.logger.warn("Canceled session before INVITE was sent"),this.stateTransition(W.Terminated);return Promise.resolve()}invite(e={}){if(this.logger.log("Inviter.invite"),this.state!==W.Initial)return super.invite(e);if(e.sessionDescriptionHandlerModifiers&&(this.sessionDescriptionHandlerModifiers=e.sessionDescriptionHandlerModifiers),e.sessionDescriptionHandlerOptions&&(this.sessionDescriptionHandlerOptions=e.sessionDescriptionHandlerOptions),e.withoutSdp||this.inviteWithoutSdp)return this._renderbody&&this._rendertype&&(this.outgoingRequestMessage.body={contentType:this._rendertype,body:this._renderbody}),this.stateTransition(W.Establishing),Promise.resolve(this.sendInvite(e));const t={sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers,sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions};return this.getOffer(t).then((t=>(this.outgoingRequestMessage.body={body:t.content,contentType:t.contentType},this.stateTransition(W.Establishing),this.sendInvite(e)))).catch((e=>{throw this.logger.log(e.message),this.stateTransition(W.Terminated),e}))}sendInvite(e={}){return this.outgoingInviteRequest=this.userAgent.userAgentCore.invite(this.outgoingRequestMessage,{onAccept:t=>this.dialog?(this.logger.log("Additional confirmed dialog, sending ACK and BYE"),void this.ackAndBye(t)):this.isCanceled?(this.logger.log("Canceled session accepted, sending ACK and BYE"),this.ackAndBye(t),void this.stateTransition(W.Terminated)):(this.notifyReferer(t),void this.onAccept(t).then((()=>{this.disposeEarlyMedia()})).catch((()=>{this.disposeEarlyMedia()})).then((()=>{e.requestDelegate&&e.requestDelegate.onAccept&&e.requestDelegate.onAccept(t)}))),onProgress:t=>{this.isCanceled||(this.notifyReferer(t),this.onProgress(t).catch((()=>{this.disposeEarlyMedia()})).then((()=>{e.requestDelegate&&e.requestDelegate.onProgress&&e.requestDelegate.onProgress(t)})))},onRedirect:t=>{this.notifyReferer(t),this.onRedirect(t),e.requestDelegate&&e.requestDelegate.onRedirect&&e.requestDelegate.onRedirect(t)},onReject:t=>{this.notifyReferer(t),this.onReject(t),e.requestDelegate&&e.requestDelegate.onReject&&e.requestDelegate.onReject(t)},onTrying:t=>{this.notifyReferer(t),this.onTrying(t),e.requestDelegate&&e.requestDelegate.onTrying&&e.requestDelegate.onTrying(t)}}),this.outgoingInviteRequest}disposeEarlyMedia(){this.earlyMediaSessionDescriptionHandlers.forEach((e=>{e.close()})),this.earlyMediaSessionDescriptionHandlers.clear()}notifyReferer(e){if(!this._referred)return;if(!(this._referred instanceof Z))throw new Error("Referred session not instance of session");if(!this._referred.dialog)return;if(!e.message.statusCode)throw new Error("Status code undefined.");if(!e.message.reasonPhrase)throw new Error("Reason phrase undefined.");const t=`SIP/2.0 ${e.message.statusCode} ${e.message.reasonPhrase}`.trim();this._referred.dialog.notify(void 0,{extraHeaders:["Event: refer","Subscription-State: terminated"],body:{contentDisposition:"render",contentType:"message/sipfrag",content:t}}).delegate={onReject:()=>{this._referred=void 0}}}onAccept(e){if(this.logger.log("Inviter.onAccept"),this.state!==W.Establishing)return this.logger.error(`Accept received while in state ${this.state}, dropping response`),Promise.reject(new Error(`Invalid session state ${this.state}`));const t=e.message,s=e.session;switch(t.hasHeader("P-Asserted-Identity")&&(this._assertedIdentity=y.nameAddrHeaderParse(t.getHeader("P-Asserted-Identity"))),s.delegate={onAck:e=>this.onAckRequest(e),onBye:e=>this.onByeRequest(e),onInfo:e=>this.onInfoRequest(e),onInvite:e=>this.onInviteRequest(e),onMessage:e=>this.onMessageRequest(e),onNotify:e=>this.onNotifyRequest(e),onPrack:e=>this.onPrackRequest(e),onRefer:e=>this.onReferRequest(e)},this._dialog=s,s.signalingState){case M.Initial:case M.HaveLocalOffer:return this.logger.error("Received 2xx response to INVITE without a session description"),this.ackAndBye(e,400,"Missing session description"),this.stateTransition(W.Terminated),Promise.reject(new Error("Bad Media Description"));case M.HaveRemoteOffer:{if(!this._dialog.offer)throw new Error(`Session offer undefined in signaling state ${this._dialog.signalingState}.`);const t={sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers,sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions};return this.setOfferAndGetAnswer(this._dialog.offer,t).then((t=>{e.ack({body:t}),this.stateTransition(W.Established)})).catch((t=>{throw this.ackAndBye(e,488,"Invalid session description"),this.stateTransition(W.Terminated),t}))}case M.Stable:{if(this.earlyMediaSessionDescriptionHandlers.size>0){const t=this.earlyMediaSessionDescriptionHandlers.get(s.id);if(!t)throw new Error("Session description handler undefined.");return this.setSessionDescriptionHandler(t),this.earlyMediaSessionDescriptionHandlers.delete(s.id),e.ack(),this.stateTransition(W.Established),Promise.resolve()}if(this.earlyMediaDialog){if(this.earlyMediaDialog!==s){if(this.earlyMedia){const e="You have set the 'earlyMedia' option to 'true' which requires that your INVITE requests do not fork and yet this INVITE request did in fact fork. Consequentially and not surprisingly the end point which accepted the INVITE (confirmed dialog) does not match the end point with which early media has been setup (early dialog) and thus this session is unable to proceed. In accordance with the SIP specifications, the SIP servers your end point is connected to determine if an INVITE forks and the forking behavior of those servers cannot be controlled by this library. If you wish to use early media with this library you must configure those servers accordingly. Alternatively you may set the 'earlyMedia' to 'false' which will allow this library to function with any INVITE requests which do fork.";this.logger.error(e)}const t=new Error("Early media dialog does not equal confirmed dialog, terminating session");return this.logger.error(t.message),this.ackAndBye(e,488,"Not Acceptable Here"),this.stateTransition(W.Terminated),Promise.reject(t)}return e.ack(),this.stateTransition(W.Established),Promise.resolve()}const t=s.answer;if(!t)throw new Error("Answer is undefined.");const i={sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers,sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions};return this.setAnswer(t,i).then((()=>{let t;this._renderbody&&this._rendertype&&(t={body:{contentDisposition:"render",contentType:this._rendertype,content:this._renderbody}}),e.ack(t),this.stateTransition(W.Established)})).catch((t=>{throw this.logger.error(t.message),this.ackAndBye(e,488,"Not Acceptable Here"),this.stateTransition(W.Terminated),t}))}case M.Closed:return Promise.reject(new Error("Terminated."));default:throw new Error("Unknown session signaling state.")}}onProgress(e){var t;if(this.logger.log("Inviter.onProgress"),this.state!==W.Establishing)return this.logger.error(`Progress received while in state ${this.state}, dropping response`),Promise.reject(new Error(`Invalid session state ${this.state}`));if(!this.outgoingInviteRequest)throw new Error("Outgoing INVITE request undefined.");const s=e.message,i=e.session;s.hasHeader("P-Asserted-Identity")&&(this._assertedIdentity=y.nameAddrHeaderParse(s.getHeader("P-Asserted-Identity")));const r=s.getHeader("require"),n=s.getHeader("rseq"),o=!!(r&&r.includes("100rel")&&n?Number(n):void 0),a=[];switch(o&&a.push("RAck: "+s.getHeader("rseq")+" "+s.getHeader("cseq")),i.signalingState){case M.Initial:return o&&(this.logger.warn("First reliable provisional response received MUST contain an offer when INVITE does not contain an offer."),e.prack({extraHeaders:a})),Promise.resolve();case M.HaveLocalOffer:return o&&e.prack({extraHeaders:a}),Promise.resolve();case M.HaveRemoteOffer:if(!o)return this.logger.warn("Non-reliable provisional response MUST NOT contain an initial offer, discarding response."),Promise.resolve();{const r=this.sessionDescriptionHandlerFactory(this,this.userAgent.configuration.sessionDescriptionHandlerFactoryOptions||{});return(null===(t=this.delegate)||void 0===t?void 0:t.onSessionDescriptionHandler)&&this.delegate.onSessionDescriptionHandler(r,!0),this.earlyMediaSessionDescriptionHandlers.set(i.id,r),r.setDescription(s.body,this.sessionDescriptionHandlerOptions,this.sessionDescriptionHandlerModifiers).then((()=>r.getDescription(this.sessionDescriptionHandlerOptions,this.sessionDescriptionHandlerModifiers))).then((t=>{const s={contentDisposition:"session",contentType:t.contentType,content:t.body};e.prack({extraHeaders:a,body:s})})).catch((e=>{throw this.stateTransition(W.Terminated),e}))}case M.Stable:if(o&&e.prack({extraHeaders:a}),this.earlyMedia&&!this.earlyMediaDialog){this.earlyMediaDialog=i;const e=i.answer;if(!e)throw new Error("Answer is undefined.");const t={sessionDescriptionHandlerModifiers:this.sessionDescriptionHandlerModifiers,sessionDescriptionHandlerOptions:this.sessionDescriptionHandlerOptions};return this.setAnswer(e,t).catch((e=>{throw this.stateTransition(W.Terminated),e}))}return Promise.resolve();case M.Closed:return Promise.reject(new Error("Terminated."));default:throw new Error("Unknown session signaling state.")}}onRedirect(e){this.logger.log("Inviter.onRedirect"),this.state===W.Establishing||this.state===W.Terminating?this.stateTransition(W.Terminated):this.logger.error(`Redirect received while in state ${this.state}, dropping response`)}onReject(e){this.logger.log("Inviter.onReject"),this.state===W.Establishing||this.state===W.Terminating?this.stateTransition(W.Terminated):this.logger.error(`Reject received while in state ${this.state}, dropping response`)}onTrying(e){this.logger.log("Inviter.onTrying"),this.state===W.Establishing||this.logger.error(`Trying received while in state ${this.state}, dropping response`)}}class Q{constructor(e,t,s,i="text/plain",r={}){this.logger=e.getLogger("sip.Messager"),r.params=r.params||{};let n=e.userAgentCore.configuration.aor;if(r.params.fromUri&&(n="string"==typeof r.params.fromUri?y.URIParse(r.params.fromUri):r.params.fromUri),!n)throw new TypeError("Invalid from URI: "+r.params.fromUri);let o=t;if(r.params.toUri&&(o="string"==typeof r.params.toUri?y.URIParse(r.params.toUri):r.params.toUri),!o)throw new TypeError("Invalid to URI: "+r.params.toUri);const a=r.params?Object.assign({},r.params):{},c=(r.extraHeaders||[]).slice(),h={contentDisposition:"render",contentType:i,content:s};this.request=e.userAgentCore.makeOutgoingRequestMessage(L.MESSAGE,t,n,o,a,c,h),this.userAgent=e}message(e={}){return this.userAgent.userAgentCore.request(this.request,e.requestDelegate),Promise.resolve()}}var ee,te,se,ie,re,ne,oe,ae;!function(e){e.Initial="Initial",e.Published="Published",e.Unpublished="Unpublished",e.Terminated="Terminated"}(ee||(ee={}));class ce{constructor(e,t,s,i={}){this.disposed=!1,this._state=ee.Initial,this._stateEventEmitter=new u,this.userAgent=e,i.extraHeaders=(i.extraHeaders||[]).slice(),i.contentType=i.contentType||"text/plain","number"!=typeof i.expires||i.expires%1!=0?i.expires=3600:i.expires=Number(i.expires),"boolean"!=typeof i.unpublishOnClose&&(i.unpublishOnClose=!0),this.target=t,this.event=s,this.options=i,this.pubRequestExpires=i.expires,this.logger=e.getLogger("sip.Publisher");const r=i.params||{},n=r.fromUri?r.fromUri:e.userAgentCore.configuration.aor,o=r.toUri?r.toUri:t;let a;if(i.body&&i.contentType){a={contentDisposition:"render",contentType:i.contentType,content:i.body}}const c=(i.extraHeaders||[]).slice();this.request=e.userAgentCore.makeOutgoingRequestMessage(L.PUBLISH,t,n,o,r,c,a),this.id=this.target.toString()+":"+this.event,this.userAgent._publishers[this.id]=this}dispose(){return this.disposed?Promise.resolve():(this.disposed=!0,this.logger.log(`Publisher ${this.id} in state ${this.state} is being disposed`),delete this.userAgent._publishers[this.id],this.options.unpublishOnClose&&this.state===ee.Published?this.unpublish():(this.publishRefreshTimer&&(clearTimeout(this.publishRefreshTimer),this.publishRefreshTimer=void 0),this.pubRequestBody=void 0,this.pubRequestExpires=0,this.pubRequestEtag=void 0,Promise.resolve()))}get state(){return this._state}get stateChange(){return this._stateEventEmitter}publish(e,t={}){if(this.publishRefreshTimer&&(clearTimeout(this.publishRefreshTimer),this.publishRefreshTimer=void 0),this.options.body=e,this.pubRequestBody=this.options.body,0===this.pubRequestExpires){if(void 0===this.options.expires)throw new Error("Expires undefined.");this.pubRequestExpires=this.options.expires,this.pubRequestEtag=void 0}return this.sendPublishRequest(),Promise.resolve()}unpublish(e={}){return this.publishRefreshTimer&&(clearTimeout(this.publishRefreshTimer),this.publishRefreshTimer=void 0),this.pubRequestBody=void 0,this.pubRequestExpires=0,void 0!==this.pubRequestEtag&&this.sendPublishRequest(),Promise.resolve()}receiveResponse(e){const t=e.statusCode||0;switch(!0){case/^1[0-9]{2}$/.test(t.toString()):break;case/^2[0-9]{2}$/.test(t.toString()):if(e.hasHeader("SIP-ETag")?this.pubRequestEtag=e.getHeader("SIP-ETag"):this.logger.warn("SIP-ETag header missing in a 200-class response to PUBLISH"),e.hasHeader("Expires")){const t=Number(e.getHeader("Expires"));"number"==typeof t&&t>=0&&t<=this.pubRequestExpires?this.pubRequestExpires=t:this.logger.warn("Bad Expires header in a 200-class response to PUBLISH")}else this.logger.warn("Expires header missing in a 200-class response to PUBLISH");0!==this.pubRequestExpires?(this.publishRefreshTimer=setTimeout((()=>this.refreshRequest()),900*this.pubRequestExpires),this._state!==ee.Published&&this.stateTransition(ee.Published)):this.stateTransition(ee.Unpublished);break;case/^412$/.test(t.toString()):if(void 0!==this.pubRequestEtag&&0!==this.pubRequestExpires){if(this.logger.warn("412 response to PUBLISH, recovering"),this.pubRequestEtag=void 0,void 0===this.options.body)throw new Error("Body undefined.");this.publish(this.options.body)}else this.logger.warn("412 response to PUBLISH, recovery failed"),this.pubRequestExpires=0,this.stateTransition(ee.Unpublished),this.stateTransition(ee.Terminated);break;case/^423$/.test(t.toString()):if(0!==this.pubRequestExpires&&e.hasHeader("Min-Expires")){const t=Number(e.getHeader("Min-Expires"));if("number"==typeof t||t>this.pubRequestExpires){if(this.logger.warn("423 code in response to PUBLISH, adjusting the Expires value and trying to recover"),this.pubRequestExpires=t,void 0===this.options.body)throw new Error("Body undefined.");this.publish(this.options.body)}else this.logger.warn("Bad 423 response Min-Expires header received for PUBLISH"),this.pubRequestExpires=0,this.stateTransition(ee.Unpublished),this.stateTransition(ee.Terminated)}else this.logger.warn("423 response to PUBLISH, recovery failed"),this.pubRequestExpires=0,this.stateTransition(ee.Unpublished),this.stateTransition(ee.Terminated);break;default:this.pubRequestExpires=0,this.stateTransition(ee.Unpublished),this.stateTransition(ee.Terminated)}0===this.pubRequestExpires&&(this.publishRefreshTimer&&(clearTimeout(this.publishRefreshTimer),this.publishRefreshTimer=void 0),this.pubRequestBody=void 0,this.pubRequestEtag=void 0)}send(){return this.userAgent.userAgentCore.publish(this.request,{onAccept:e=>this.receiveResponse(e.message),onProgress:e=>this.receiveResponse(e.message),onRedirect:e=>this.receiveResponse(e.message),onReject:e=>this.receiveResponse(e.message),onTrying:e=>this.receiveResponse(e.message)})}refreshRequest(){if(this.publishRefreshTimer&&(clearTimeout(this.publishRefreshTimer),this.publishRefreshTimer=void 0),this.pubRequestBody=void 0,void 0===this.pubRequestEtag)throw new Error("Etag undefined");if(0===this.pubRequestExpires)throw new Error("Expires zero");this.sendPublishRequest()}sendPublishRequest(){const e=Object.assign({},this.options);e.extraHeaders=(this.options.extraHeaders||[]).slice(),e.extraHeaders.push("Event: "+this.event),e.extraHeaders.push("Expires: "+this.pubRequestExpires),void 0!==this.pubRequestEtag&&e.extraHeaders.push("SIP-If-Match: "+this.pubRequestEtag);const t=this.target,s=this.options.params||{};let i,r;if(void 0!==this.pubRequestBody){if(void 0===this.options.contentType)throw new Error("Content type undefined.");i={body:this.pubRequestBody,contentType:this.options.contentType}}return i&&(r=P(i)),this.request=this.userAgent.userAgentCore.makeOutgoingRequestMessage(L.PUBLISH,t,s.fromUri?s.fromUri:this.userAgent.userAgentCore.configuration.aor,s.toUri?s.toUri:this.target,s,e.extraHeaders,r),this.send()}stateTransition(e){const t=()=>{throw new Error(`Invalid state transition from ${this._state} to ${e}`)};switch(this._state){case ee.Initial:e!==ee.Published&&e!==ee.Unpublished&&e!==ee.Terminated&&t();break;case ee.Published:e!==ee.Unpublished&&e!==ee.Terminated&&t();break;case ee.Unpublished:e!==ee.Published&&e!==ee.Terminated&&t();break;case ee.Terminated:t();break;default:throw new Error("Unrecognized state.")}this._state=e,this.logger.log(`Publication transitioned to state ${this._state}`),this._stateEventEmitter.emit(this._state),e===ee.Terminated&&this.dispose()}}!function(e){e.Initial="Initial",e.Registered="Registered",e.Unregistered="Unregistered",e.Terminated="Terminated"}(te||(te={}));class he{constructor(e,t={}){this.disposed=!1,this._contacts=[],this._retryAfter=void 0,this._state=te.Initial,this._waiting=!1,this._stateEventEmitter=new u,this._waitingEventEmitter=new u,this.userAgent=e;const s=e.configuration.uri.clone();if(s.user=void 0,this.options=Object.assign(Object.assign(Object.assign({},he.defaultOptions()),{registrar:s}),he.stripUndefinedProperties(t)),this.options.extraContactHeaderParams=(this.options.extraContactHeaderParams||[]).slice(),this.options.extraHeaders=(this.options.extraHeaders||[]).slice(),!this.options.registrar)throw new Error("Registrar undefined.");if(this.options.registrar=this.options.registrar.clone(),this.options.regId&&!this.options.instanceId?this.options.instanceId=he.newUUID():!this.options.regId&&this.options.instanceId&&(this.options.regId=1),this.options.instanceId&&-1===y.parse(this.options.instanceId,"uuid"))throw new Error("Invalid instanceId.");if(this.options.regId&&this.options.regId<0)throw new Error("Invalid regId.");const i=this.options.registrar,r=this.options.params&&this.options.params.fromUri||e.userAgentCore.configuration.aor,n=this.options.params&&this.options.params.toUri||e.configuration.uri,o=this.options.params||{},a=(t.extraHeaders||[]).slice();if(this.request=e.userAgentCore.makeOutgoingRequestMessage(L.REGISTER,i,r,n,o,a,void 0),this.expires=this.options.expires||he.defaultExpires,this.expires<0)throw new Error("Invalid expires.");if(this.refreshFrequency=this.options.refreshFrequency||he.defaultRefreshFrequency,this.refreshFrequency<50||this.refreshFrequency>99)throw new Error("Invalid refresh frequency. The value represents a percentage of the expiration time and should be between 50 and 99.");this.logger=e.getLogger("sip.Registerer"),this.options.logConfiguration&&(this.logger.log("Configuration:"),Object.keys(this.options).forEach((e=>{const t=this.options[e];switch(e){case"registrar":this.logger.log("\xb7 "+e+": "+t);break;default:this.logger.log("\xb7 "+e+": "+JSON.stringify(t))}}))),this.id=this.request.callId+this.request.from.parameters.tag,this.userAgent._registerers[this.id]=this}static defaultOptions(){return{expires:he.defaultExpires,extraContactHeaderParams:[],extraHeaders:[],logConfiguration:!0,instanceId:"",params:{},regId:0,registrar:new v("sip","anonymous","anonymous.invalid"),refreshFrequency:he.defaultRefreshFrequency}}static newUUID(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,(e=>{const t=Math.floor(16*Math.random());return("x"===e?t:t%4+8).toString(16)}))}static stripUndefinedProperties(e){return Object.keys(e).reduce(((t,s)=>(void 0!==e[s]&&(t[s]=e[s]),t)),{})}get contacts(){return this._contacts.slice()}get retryAfter(){return this._retryAfter}get state(){return this._state}get stateChange(){return this._stateEventEmitter}dispose(){return this.disposed?Promise.resolve():(this.disposed=!0,this.logger.log(`Registerer ${this.id} in state ${this.state} is being disposed`),delete this.userAgent._registerers[this.id],new Promise((e=>{const t=()=>{if(!this.waiting&&this._state===te.Registered)return this.stateChange.addListener((()=>{this.terminated(),e()}),{once:!0}),void this.unregister();this.terminated(),e()};this.waiting?this.waitingChange.addListener((()=>{t()}),{once:!0}):t()})))}register(e={}){if(this.state===te.Terminated)throw this.stateError(),new Error("Registerer terminated. Unable to register.");if(this.disposed)throw this.stateError(),new Error("Registerer disposed. Unable to register.");if(this.waiting){this.waitingWarning();const e=new a("REGISTER request already in progress, waiting for final response");return Promise.reject(e)}e.requestOptions&&(this.options=Object.assign(Object.assign({},this.options),e.requestOptions));const t=(this.options.extraHeaders||[]).slice();t.push("Contact: "+this.generateContactHeader(this.expires)),t.push("Allow: "+["ACK","CANCEL","INVITE","MESSAGE","BYE","OPTIONS","INFO","NOTIFY","REFER"].toString()),this.request.cseq++,this.request.setHeader("cseq",this.request.cseq+" REGISTER"),this.request.extraHeaders=t,this.waitingToggle(!0);const s=this.userAgent.userAgentCore.register(this.request,{onAccept:t=>{let s;t.message.hasHeader("expires")&&(s=Number(t.message.getHeader("expires"))),this._contacts=t.message.getHeaders("contact");let i,r=this._contacts.length;if(!r)return this.logger.error("No Contact header in response to REGISTER, dropping response."),void this.unregistered();for(;r--;){if(i=t.message.parseHeader("contact",r),!i)throw new Error("Contact undefined");if(this.userAgent.contact.pubGruu&&w(i.uri,this.userAgent.contact.pubGruu)){s=Number(i.getParam("expires"));break}if(""===this.userAgent.configuration.contactName){if(i.uri.user===this.userAgent.contact.uri.user){s=Number(i.getParam("expires"));break}}else if(w(i.uri,this.userAgent.contact.uri)){s=Number(i.getParam("expires"));break}i=void 0}if(void 0===i)return this.logger.error("No Contact header pointing to us, dropping response"),this.unregistered(),void this.waitingToggle(!1);if(void 0===s)return this.logger.error("Contact pointing to us is missing expires parameter, dropping response"),this.unregistered(),void this.waitingToggle(!1);if(i.hasParam("temp-gruu")){const e=i.getParam("temp-gruu");e&&(this.userAgent.contact.tempGruu=y.URIParse(e.replace(/"/g,"")))}if(i.hasParam("pub-gruu")){const e=i.getParam("pub-gruu");e&&(this.userAgent.contact.pubGruu=y.URIParse(e.replace(/"/g,"")))}this.registered(s),e.requestDelegate&&e.requestDelegate.onAccept&&e.requestDelegate.onAccept(t),this.waitingToggle(!1)},onProgress:t=>{e.requestDelegate&&e.requestDelegate.onProgress&&e.requestDelegate.onProgress(t)},onRedirect:t=>{this.logger.error("Redirect received. Not supported."),this.unregistered(),e.requestDelegate&&e.requestDelegate.onRedirect&&e.requestDelegate.onRedirect(t),this.waitingToggle(!1)},onReject:t=>{if(423===t.message.statusCode)return t.message.hasHeader("min-expires")?(this.expires=Number(t.message.getHeader("min-expires")),this.waitingToggle(!1),void this.register()):(this.logger.error("423 response received for REGISTER without Min-Expires, dropping response"),this.unregistered(),void this.waitingToggle(!1));this.logger.warn(`Failed to register, status code ${t.message.statusCode}`);let s=NaN;if(500===t.message.statusCode||503===t.message.statusCode){const e=t.message.getHeader("retry-after");e&&(s=Number.parseInt(e,void 0))}this._retryAfter=isNaN(s)?void 0:s,this.unregistered(),e.requestDelegate&&e.requestDelegate.onReject&&e.requestDelegate.onReject(t),this._retryAfter=void 0,this.waitingToggle(!1)},onTrying:t=>{e.requestDelegate&&e.requestDelegate.onTrying&&e.requestDelegate.onTrying(t)}});return Promise.resolve(s)}unregister(e={}){if(this.state===te.Terminated)throw this.stateError(),new Error("Registerer terminated. Unable to register.");if(this.disposed&&this.state!==te.Registered)throw this.stateError(),new Error("Registerer disposed. Unable to register.");if(this.waiting){this.waitingWarning();const e=new a("REGISTER request already in progress, waiting for final response");return Promise.reject(e)}this._state===te.Registered||e.all||this.logger.warn("Not currently registered, but sending an unregister anyway.");const t=(e.requestOptions&&e.requestOptions.extraHeaders||[]).slice();this.request.extraHeaders=t,e.all?(t.push("Contact: *"),t.push("Expires: 0")):t.push("Contact: "+this.generateContactHeader(0)),this.request.cseq++,this.request.setHeader("cseq",this.request.cseq+" REGISTER"),void 0!==this.registrationTimer&&(clearTimeout(this.registrationTimer),this.registrationTimer=void 0),this.waitingToggle(!0);const s=this.userAgent.userAgentCore.register(this.request,{onAccept:t=>{this._contacts=t.message.getHeaders("contact"),this.unregistered(),e.requestDelegate&&e.requestDelegate.onAccept&&e.requestDelegate.onAccept(t),this.waitingToggle(!1)},onProgress:t=>{e.requestDelegate&&e.requestDelegate.onProgress&&e.requestDelegate.onProgress(t)},onRedirect:t=>{this.logger.error("Unregister redirected. Not currently supported."),this.unregistered(),e.requestDelegate&&e.requestDelegate.onRedirect&&e.requestDelegate.onRedirect(t),this.waitingToggle(!1)},onReject:t=>{this.logger.error(`Unregister rejected with status code ${t.message.statusCode}`),this.unregistered(),e.requestDelegate&&e.requestDelegate.onReject&&e.requestDelegate.onReject(t),this.waitingToggle(!1)},onTrying:t=>{e.requestDelegate&&e.requestDelegate.onTrying&&e.requestDelegate.onTrying(t)}});return Promise.resolve(s)}clearTimers(){void 0!==this.registrationTimer&&(clearTimeout(this.registrationTimer),this.registrationTimer=void 0),void 0!==this.registrationExpiredTimer&&(clearTimeout(this.registrationExpiredTimer),this.registrationExpiredTimer=void 0)}generateContactHeader(e){let t=this.userAgent.contact.toString();return this.options.regId&&this.options.instanceId&&(t+=";reg-id="+this.options.regId,t+=';+sip.instance="<urn:uuid:'+this.options.instanceId+'>"'),this.options.extraContactHeaderParams&&this.options.extraContactHeaderParams.forEach((e=>{t+=";"+e})),t+=";expires="+e,t}registered(e){this.clearTimers(),this.registrationTimer=setTimeout((()=>{this.registrationTimer=void 0,this.register()}),this.refreshFrequency/100*e*1e3),this.registrationExpiredTimer=setTimeout((()=>{this.logger.warn("Registration expired"),this.unregistered()}),1e3*e),this._state!==te.Registered&&this.stateTransition(te.Registered)}unregistered(){this.clearTimers(),this._state!==te.Unregistered&&this.stateTransition(te.Unregistered)}terminated(){this.clearTimers(),this._state!==te.Terminated&&this.stateTransition(te.Terminated)}stateTransition(e){const t=()=>{throw new Error(`Invalid state transition from ${this._state} to ${e}`)};switch(this._state){case te.Initial:e!==te.Registered&&e!==te.Unregistered&&e!==te.Terminated&&t();break;case te.Registered:e!==te.Unregistered&&e!==te.Terminated&&t();break;case te.Unregistered:e!==te.Registered&&e!==te.Terminated&&t();break;case te.Terminated:t();break;default:throw new Error("Unrecognized state.")}this._state=e,this.logger.log(`Registration transitioned to state ${this._state}`),this._stateEventEmitter.emit(this._state),e===te.Terminated&&this.dispose()}get waiting(){return this._waiting}get waitingChange(){return this._waitingEventEmitter}waitingToggle(e){if(this._waiting===e)throw new Error(`Invalid waiting transition from ${this._waiting} to ${e}`);this._waiting=e,this.logger.log(`Waiting toggled to ${this._waiting}`),this._waitingEventEmitter.emit(this._waiting)}waitingWarning(){let e="An attempt was made to send a REGISTER request while a prior one was still in progress.";e+=" RFC 3261 requires UAs MUST NOT send a new registration until they have received a final response",e+=" from the registrar for the previous one or the previous REGISTER request has timed out.",e+=" Note that if the transport disconnects, you still must wait for the prior request to time out before",e+=" sending a new REGISTER request or alternatively dispose of the current Registerer and create a new Registerer.",this.logger.warn("An attempt was made to send a REGISTER request while a prior one was still in progress. RFC 3261 requires UAs MUST NOT send a new registration until they have received a final response from the registrar for the previous one or the previous REGISTER request has timed out. Note that if the transport disconnects, you still must wait for the prior request to time out before sending a new REGISTER request or alternatively dispose of the current Registerer and create a new Registerer.")}stateError(){let e=`An attempt was made to send a REGISTER request when the Registerer ${this.state===te.Terminated?"is in 'Terminated' state":"has been disposed"}.`;e+=" The Registerer transitions to 'Terminated' when Registerer.dispose() is called.",e+=" Perhaps you called UserAgent.stop() which dipsoses of all Registerers?",this.logger.error(e)}}he.defaultExpires=600,he.defaultRefreshFrequency=99,function(e){e.Initial="Initial",e.NotifyWait="NotifyWait",e.Pending="Pending",e.Active="Active",e.Terminated="Terminated"}(se||(se={})),function(e){e.Initial="Initial",e.NotifyWait="NotifyWait",e.Subscribed="Subscribed",e.Terminated="Terminated"}(ie||(ie={}));class de{constructor(e,t={}){this._disposed=!1,this._state=ie.Initial,this._logger=e.getLogger("sip.Subscription"),this._stateEventEmitter=new u,this._userAgent=e,this.delegate=t.delegate}dispose(){return this._disposed||(this._disposed=!0,this._stateEventEmitter.removeAllListeners()),Promise.resolve()}get dialog(){return this._dialog}get disposed(){return this._disposed}get state(){return this._state}get stateChange(){return this._stateEventEmitter}stateTransition(e){const t=()=>{throw new Error(`Invalid state transition from ${this._state} to ${e}`)};switch(this._state){case ie.Initial:e!==ie.NotifyWait&&e!==ie.Terminated&&t();break;case ie.NotifyWait:e!==ie.Subscribed&&e!==ie.Terminated&&t();break;case ie.Subscribed:e!==ie.Terminated&&t();break;case ie.Terminated:t();break;default:throw new Error("Unrecognized state.")}this._state!==e&&(this._state=e,this._logger.log(`Subscription ${this._dialog?this._dialog.id:void 0} transitioned to ${this._state}`),this._stateEventEmitter.emit(this._state),e===ie.Terminated&&this.dispose())}}class le extends de{constructor(e,t,s,i={}){super(e,i),this.body=void 0,this.logger=e.getLogger("sip.Subscriber"),i.body&&(this.body={body:i.body,contentType:i.contentType?i.contentType:"application/sdp"}),this.targetURI=t,this.event=s,void 0===i.expires?this.expires=3600:"number"!=typeof i.expires?(this.logger.warn('Option "expires" must be a number. Using default of 3600.'),this.expires=3600):this.expires=i.expires,this.extraHeaders=(i.extraHeaders||[]).slice(),this.subscriberRequest=this.initSubscriberRequest(),this.outgoingRequestMessage=this.subscriberRequest.message,this.id=this.outgoingRequestMessage.callId+this.outgoingRequestMessage.from.parameters.tag+this.event,this._userAgent._subscriptions[this.id]=this}dispose(){return this.disposed?Promise.resolve():(this.logger.log(`Subscription ${this.id} in state ${this.state} is being disposed`),delete this._userAgent._subscriptions[this.id],this.retryAfterTimer&&(clearTimeout(this.retryAfterTimer),this.retryAfterTimer=void 0),this.subscriberRequest.dispose(),super.dispose().then((()=>{if(this.state===ie.Subscribed){if(!this._dialog)throw new Error("Dialog undefined.");if(this._dialog.subscriptionState===se.Pending||this._dialog.subscriptionState===se.Active){const e=this._dialog;return new Promise(((t,s)=>{e.delegate={onTerminated:()=>t()},e.unsubscribe()}))}}})))}subscribe(e={}){switch(this.subscriberRequest.state){case se.Initial:this.state===ie.Initial&&this.stateTransition(ie.NotifyWait),this.subscriberRequest.subscribe().then((e=>{e.success?(e.success.subscription&&(this._dialog=e.success.subscription,this._dialog.delegate={onNotify:e=>this.onNotify(e),onRefresh:e=>this.onRefresh(e),onTerminated:()=>{this.state!==ie.Terminated&&this.stateTransition(ie.Terminated)}}),this.onNotify(e.success.request)):e.failure&&this.unsubscribe()}));break;case se.NotifyWait:case se.Pending:break;case se.Active:if(this._dialog){this._dialog.refresh().delegate={onAccept:e=>this.onAccepted(e),onRedirect:e=>this.unsubscribe(),onReject:e=>this.unsubscribe()}}break;case se.Terminated:}return Promise.resolve()}unsubscribe(e={}){if(this.disposed)return Promise.resolve();switch(this.subscriberRequest.state){case se.Initial:case se.NotifyWait:break;case se.Pending:case se.Active:this._dialog&&this._dialog.unsubscribe();break;case se.Terminated:break;default:throw new Error("Unknown state.")}return this.stateTransition(ie.Terminated),Promise.resolve()}_refresh(){return this.subscriberRequest.state===se.Active?this.subscribe():Promise.resolve()}onAccepted(e){}onNotify(e){if(this.disposed)return void e.accept();if(this.state!==ie.Subscribed&&this.stateTransition(ie.Subscribed),this.delegate&&this.delegate.onNotify){const t=new V(e);this.delegate.onNotify(t)}else e.accept();const t=e.message.parseHeader("Subscription-State");if(t&&t.state)switch(t.state){case"terminated":if(t.reason)switch(this.logger.log(`Terminated subscription with reason ${t.reason}`),t.reason){case"deactivated":case"timeout":return this.initSubscriberRequest(),void this.subscribe();case"probation":case"giveup":return this.initSubscriberRequest(),void(t.params&&t.params["retry-after"]?this.retryAfterTimer=setTimeout((()=>{this.subscribe()}),t.params["retry-after"]):this.subscribe())}this.unsubscribe()}}onRefresh(e){e.delegate={onAccept:e=>this.onAccepted(e)}}initSubscriberRequest(){const e={extraHeaders:this.extraHeaders,body:this.body?P(this.body):void 0};return this.subscriberRequest=new ge(this._userAgent.userAgentCore,this.targetURI,this.event,this.expires,e),this.subscriberRequest.delegate={onAccept:e=>this.onAccepted(e)},this.subscriberRequest}}class ge{constructor(e,t,s,i,r,n){this.core=e,this.target=t,this.event=s,this.expires=i,this.subscribed=!1,this.logger=e.loggerFactory.getLogger("sip.Subscriber"),this.delegate=n;const o="Allow: "+B.toString(),a=(r&&r.extraHeaders||[]).slice();a.push(o),a.push("Event: "+this.event),a.push("Expires: "+this.expires),a.push("Contact: "+this.core.configuration.contact.toString());const c=r&&r.body;this.message=e.makeOutgoingRequestMessage(L.SUBSCRIBE,this.target,this.core.configuration.aor,this.target,{},a,c)}dispose(){this.request&&(this.request.waitNotifyStop(),this.request.dispose(),this.request=void 0)}get state(){return this.subscription?this.subscription.subscriptionState:this.subscribed?se.NotifyWait:se.Initial}subscribe(){return this.subscribed?Promise.reject(new Error("Not in initial state. Did you call subscribe more than once?")):(this.subscribed=!0,new Promise((e=>{if(!this.message)throw new Error("Message undefined.");this.request=this.core.subscribe(this.message,{onAccept:e=>{this.delegate&&this.delegate.onAccept&&this.delegate.onAccept(e)},onNotify:t=>{this.subscription=t.subscription,this.subscription&&(this.subscription.autoRefresh=!0),e({success:t})},onNotifyTimeout:()=>{e({failure:{}})},onRedirect:t=>{e({failure:{response:t}})},onReject:t=>{e({failure:{response:t}})}})})))}}!function(e){e.Connecting="Connecting",e.Connected="Connected",e.Disconnecting="Disconnecting",e.Disconnected="Disconnected"}(re||(re={})),function(e){e.Started="Started",e.Stopped="Stopped"}(ne||(ne={})),function(e){e[e.error=0]="error",e[e.warn=1]="warn",e[e.log=2]="log",e[e.debug=3]="debug"}(oe||(oe={}));class ue{constructor(e,t,s){this.logger=e,this.category=t,this.label=s}error(e){this.genericLog(oe.error,e)}warn(e){this.genericLog(oe.warn,e)}log(e){this.genericLog(oe.log,e)}debug(e){this.genericLog(oe.debug,e)}genericLog(e,t){this.logger.genericLog(e,this.category,this.label,t)}get level(){return this.logger.level}set level(e){this.logger.level=e}}class pe{constructor(){this.builtinEnabled=!0,this._level=oe.log,this.loggers={},this.logger=this.getLogger("sip:loggerfactory")}get level(){return this._level}set level(e){e>=0&&e<=3?this._level=e:e>3?this._level=3:oe.hasOwnProperty(e)?this._level=e:this.logger.error("invalid 'level' parameter value: "+JSON.stringify(e))}get connector(){return this._connector}set connector(e){e?"function"==typeof e?this._connector=e:this.logger.error("invalid 'connector' parameter value: "+JSON.stringify(e)):this._connector=void 0}getLogger(e,t){if(t&&3===this.level)return new ue(this,e,t);if(this.loggers[e])return this.loggers[e];{const t=new ue(this,e);return this.loggers[e]=t,t}}genericLog(e,t,s,i){this.level>=e&&this.builtinEnabled&&this.print(e,t,s,i),this.connector&&this.connector(oe[e],t,s,i)}print(e,t,s,i){if("string"==typeof i){const e=[new Date,t];s&&e.push(s),i=e.concat(i).join(" | ")}switch(e){case oe.error:console.error(i);break;case oe.warn:console.warn(i);break;case oe.log:console.log(i);break;case oe.debug:console.debug(i)}}}class fe{constructor(){this._dataLength=0,this._bufferLength=0,this._state=new Int32Array(4),this._buffer=new ArrayBuffer(68),this._buffer8=new Uint8Array(this._buffer,0,68),this._buffer32=new Uint32Array(this._buffer,0,17),this.start()}static hashStr(e,t=!1){return this.onePassHasher.start().appendStr(e).end(t)}static hashAsciiStr(e,t=!1){return this.onePassHasher.start().appendAsciiStr(e).end(t)}static _hex(e){const t=fe.hexChars,s=fe.hexOut;let i,r,n,o;for(o=0;o<4;o+=1)for(r=8*o,i=e[o],n=0;n<8;n+=2)s[r+1+n]=t.charAt(15&i),i>>>=4,s[r+0+n]=t.charAt(15&i),i>>>=4;return s.join("")}static _md5cycle(e,t){let s=e[0],i=e[1],r=e[2],n=e[3];s+=(i&r|~i&n)+t[0]-680876936|0,s=(s<<7|s>>>25)+i|0,n+=(s&i|~s&r)+t[1]-389564586|0,n=(n<<12|n>>>20)+s|0,r+=(n&s|~n&i)+t[2]+606105819|0,r=(r<<17|r>>>15)+n|0,i+=(r&n|~r&s)+t[3]-1044525330|0,i=(i<<22|i>>>10)+r|0,s+=(i&r|~i&n)+t[4]-176418897|0,s=(s<<7|s>>>25)+i|0,n+=(s&i|~s&r)+t[5]+1200080426|0,n=(n<<12|n>>>20)+s|0,r+=(n&s|~n&i)+t[6]-1473231341|0,r=(r<<17|r>>>15)+n|0,i+=(r&n|~r&s)+t[7]-45705983|0,i=(i<<22|i>>>10)+r|0,s+=(i&r|~i&n)+t[8]+1770035416|0,s=(s<<7|s>>>25)+i|0,n+=(s&i|~s&r)+t[9]-1958414417|0,n=(n<<12|n>>>20)+s|0,r+=(n&s|~n&i)+t[10]-42063|0,r=(r<<17|r>>>15)+n|0,i+=(r&n|~r&s)+t[11]-1990404162|0,i=(i<<22|i>>>10)+r|0,s+=(i&r|~i&n)+t[12]+1804603682|0,s=(s<<7|s>>>25)+i|0,n+=(s&i|~s&r)+t[13]-40341101|0,n=(n<<12|n>>>20)+s|0,r+=(n&s|~n&i)+t[14]-1502002290|0,r=(r<<17|r>>>15)+n|0,i+=(r&n|~r&s)+t[15]+1236535329|0,i=(i<<22|i>>>10)+r|0,s+=(i&n|r&~n)+t[1]-165796510|0,s=(s<<5|s>>>27)+i|0,n+=(s&r|i&~r)+t[6]-1069501632|0,n=(n<<9|n>>>23)+s|0,r+=(n&i|s&~i)+t[11]+643717713|0,r=(r<<14|r>>>18)+n|0,i+=(r&s|n&~s)+t[0]-373897302|0,i=(i<<20|i>>>12)+r|0,s+=(i&n|r&~n)+t[5]-701558691|0,s=(s<<5|s>>>27)+i|0,n+=(s&r|i&~r)+t[10]+38016083|0,n=(n<<9|n>>>23)+s|0,r+=(n&i|s&~i)+t[15]-660478335|0,r=(r<<14|r>>>18)+n|0,i+=(r&s|n&~s)+t[4]-405537848|0,i=(i<<20|i>>>12)+r|0,s+=(i&n|r&~n)+t[9]+568446438|0,s=(s<<5|s>>>27)+i|0,n+=(s&r|i&~r)+t[14]-1019803690|0,n=(n<<9|n>>>23)+s|0,r+=(n&i|s&~i)+t[3]-187363961|0,r=(r<<14|r>>>18)+n|0,i+=(r&s|n&~s)+t[8]+1163531501|0,i=(i<<20|i>>>12)+r|0,s+=(i&n|r&~n)+t[13]-1444681467|0,s=(s<<5|s>>>27)+i|0,n+=(s&r|i&~r)+t[2]-51403784|0,n=(n<<9|n>>>23)+s|0,r+=(n&i|s&~i)+t[7]+1735328473|0,r=(r<<14|r>>>18)+n|0,i+=(r&s|n&~s)+t[12]-1926607734|0,i=(i<<20|i>>>12)+r|0,s+=(i^r^n)+t[5]-378558|0,s=(s<<4|s>>>28)+i|0,n+=(s^i^r)+t[8]-2022574463|0,n=(n<<11|n>>>21)+s|0,r+=(n^s^i)+t[11]+1839030562|0,r=(r<<16|r>>>16)+n|0,i+=(r^n^s)+t[14]-35309556|0,i=(i<<23|i>>>9)+r|0,s+=(i^r^n)+t[1]-1530992060|0,s=(s<<4|s>>>28)+i|0,n+=(s^i^r)+t[4]+1272893353|0,n=(n<<11|n>>>21)+s|0,r+=(n^s^i)+t[7]-155497632|0,r=(r<<16|r>>>16)+n|0,i+=(r^n^s)+t[10]-1094730640|0,i=(i<<23|i>>>9)+r|0,s+=(i^r^n)+t[13]+681279174|0,s=(s<<4|s>>>28)+i|0,n+=(s^i^r)+t[0]-358537222|0,n=(n<<11|n>>>21)+s|0,r+=(n^s^i)+t[3]-722521979|0,r=(r<<16|r>>>16)+n|0,i+=(r^n^s)+t[6]+76029189|0,i=(i<<23|i>>>9)+r|0,s+=(i^r^n)+t[9]-640364487|0,s=(s<<4|s>>>28)+i|0,n+=(s^i^r)+t[12]-421815835|0,n=(n<<11|n>>>21)+s|0,r+=(n^s^i)+t[15]+530742520|0,r=(r<<16|r>>>16)+n|0,i+=(r^n^s)+t[2]-995338651|0,i=(i<<23|i>>>9)+r|0,s+=(r^(i|~n))+t[0]-198630844|0,s=(s<<6|s>>>26)+i|0,n+=(i^(s|~r))+t[7]+1126891415|0,n=(n<<10|n>>>22)+s|0,r+=(s^(n|~i))+t[14]-1416354905|0,r=(r<<15|r>>>17)+n|0,i+=(n^(r|~s))+t[5]-57434055|0,i=(i<<21|i>>>11)+r|0,s+=(r^(i|~n))+t[12]+1700485571|0,s=(s<<6|s>>>26)+i|0,n+=(i^(s|~r))+t[3]-1894986606|0,n=(n<<10|n>>>22)+s|0,r+=(s^(n|~i))+t[10]-1051523|0,r=(r<<15|r>>>17)+n|0,i+=(n^(r|~s))+t[1]-2054922799|0,i=(i<<21|i>>>11)+r|0,s+=(r^(i|~n))+t[8]+1873313359|0,s=(s<<6|s>>>26)+i|0,n+=(i^(s|~r))+t[15]-30611744|0,n=(n<<10|n>>>22)+s|0,r+=(s^(n|~i))+t[6]-1560198380|0,r=(r<<15|r>>>17)+n|0,i+=(n^(r|~s))+t[13]+1309151649|0,i=(i<<21|i>>>11)+r|0,s+=(r^(i|~n))+t[4]-145523070|0,s=(s<<6|s>>>26)+i|0,n+=(i^(s|~r))+t[11]-1120210379|0,n=(n<<10|n>>>22)+s|0,r+=(s^(n|~i))+t[2]+718787259|0,r=(r<<15|r>>>17)+n|0,i+=(n^(r|~s))+t[9]-343485551|0,i=(i<<21|i>>>11)+r|0,e[0]=s+e[0]|0,e[1]=i+e[1]|0,e[2]=r+e[2]|0,e[3]=n+e[3]|0}start(){return this._dataLength=0,this._bufferLength=0,this._state.set(fe.stateIdentity),this}appendStr(e){const t=this._buffer8,s=this._buffer32;let i,r,n=this._bufferLength;for(r=0;r<e.length;r+=1){if(i=e.charCodeAt(r),i<128)t[n++]=i;else if(i<2048)t[n++]=192+(i>>>6),t[n++]=63&i|128;else if(i<55296||i>56319)t[n++]=224+(i>>>12),t[n++]=i>>>6&63|128,t[n++]=63&i|128;else{if(i=1024*(i-55296)+(e.charCodeAt(++r)-56320)+65536,i>1114111)throw new Error("Unicode standard supports code points up to U+10FFFF");t[n++]=240+(i>>>18),t[n++]=i>>>12&63|128,t[n++]=i>>>6&63|128,t[n++]=63&i|128}n>=64&&(this._dataLength+=64,fe._md5cycle(this._state,s),n-=64,s[0]=s[16])}return this._bufferLength=n,this}appendAsciiStr(e){const t=this._buffer8,s=this._buffer32;let i,r=this._bufferLength,n=0;for(;;){for(i=Math.min(e.length-n,64-r);i--;)t[r++]=e.charCodeAt(n++);if(r<64)break;this._dataLength+=64,fe._md5cycle(this._state,s),r=0}return this._bufferLength=r,this}appendByteArray(e){const t=this._buffer8,s=this._buffer32;let i,r=this._bufferLength,n=0;for(;;){for(i=Math.min(e.length-n,64-r);i--;)t[r++]=e[n++];if(r<64)break;this._dataLength+=64,fe._md5cycle(this._state,s),r=0}return this._bufferLength=r,this}getState(){const e=this,t=e._state;return{buffer:String.fromCharCode.apply(null,e._buffer8),buflen:e._bufferLength,length:e._dataLength,state:[t[0],t[1],t[2],t[3]]}}setState(e){const t=e.buffer,s=e.state,i=this._state;let r;for(this._dataLength=e.length,this._bufferLength=e.buflen,i[0]=s[0],i[1]=s[1],i[2]=s[2],i[3]=s[3],r=0;r<t.length;r+=1)this._buffer8[r]=t.charCodeAt(r)}end(e=!1){const t=this._bufferLength,s=this._buffer8,i=this._buffer32,r=1+(t>>2);let n;if(this._dataLength+=t,s[t]=128,s[t+1]=s[t+2]=s[t+3]=0,i.set(fe.buffer32Identity.subarray(r),r),t>55&&(fe._md5cycle(this._state,i),i.set(fe.buffer32Identity)),n=8*this._dataLength,n<=4294967295)i[14]=n;else{const e=n.toString(16).match(/(.*?)(.{0,8})$/);if(null===e)return;const t=parseInt(e[2],16),s=parseInt(e[1],16)||0;i[14]=t,i[15]=s}return fe._md5cycle(this._state,i),e?this._state:fe._hex(this._state)}}function me(e){return fe.hashStr(e)}fe.stateIdentity=new Int32Array([1732584193,-271733879,-1732584194,271733878]),fe.buffer32Identity=new Int32Array([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]),fe.hexChars="0123456789abcdef",fe.hexOut=[],fe.onePassHasher=new fe,"5d41402abc4b2a76b9719d911017c592"!==fe.hashStr("hello")&&console.error("Md5 self test failed.");class ve{constructor(e,t,s,i){this.logger=e.getLogger("sipjs.digestauthentication"),this.username=s,this.password=i,this.ha1=t,this.nc=0,this.ncHex="00000000"}authenticate(e,t,s){if(this.algorithm=t.algorithm,this.realm=t.realm,this.nonce=t.nonce,this.opaque=t.opaque,this.stale=t.stale,this.algorithm){if("MD5"!==this.algorithm)return this.logger.warn("challenge with Digest algorithm different than 'MD5', authentication aborted"),!1}else this.algorithm="MD5";if(!this.realm)return this.logger.warn("challenge without Digest realm, authentication aborted"),!1;if(!this.nonce)return this.logger.warn("challenge without Digest nonce, authentication aborted"),!1;if(t.qop)if(t.qop.indexOf("auth")>-1)this.qop="auth";else{if(!(t.qop.indexOf("auth-int")>-1))return this.logger.warn("challenge without Digest qop different than 'auth' or 'auth-int', authentication aborted"),!1;this.qop="auth-int"}else this.qop=void 0;return this.method=e.method,this.uri=e.ruri,this.cnonce=R(12),this.nc+=1,this.updateNcHex(),4294967296===this.nc&&(this.nc=1,this.ncHex="00000001"),this.calculateResponse(s),!0}toString(){const e=[];if(!this.response)throw new Error("response field does not exist, cannot generate Authorization header");return e.push("algorithm="+this.algorithm),e.push('username="'+this.username+'"'),e.push('realm="'+this.realm+'"'),e.push('nonce="'+this.nonce+'"'),e.push('uri="'+this.uri+'"'),e.push('response="'+this.response+'"'),this.opaque&&e.push('opaque="'+this.opaque+'"'),this.qop&&(e.push("qop="+this.qop),e.push('cnonce="'+this.cnonce+'"'),e.push("nc="+this.ncHex)),"Digest "+e.join(", ")}updateNcHex(){const e=Number(this.nc).toString(16);this.ncHex="00000000".substr(0,8-e.length)+e}calculateResponse(e){let t,s;t=this.ha1,""!==t&&void 0!==t||(t=me(this.username+":"+this.realm+":"+this.password)),"auth"===this.qop?(s=me(this.method+":"+this.uri),this.response=me(t+":"+this.nonce+":"+this.ncHex+":"+this.cnonce+":auth:"+s)):"auth-int"===this.qop?(s=me(this.method+":"+this.uri+":"+me(e||"")),this.response=me(t+":"+this.nonce+":"+this.ncHex+":"+this.cnonce+":auth-int:"+s)):void 0===this.qop&&(s=me(this.method+":"+this.uri),this.response=me(t+":"+this.nonce+":"+s))}}function we(e,t){const s="\r\n";if(t.statusCode<100||t.statusCode>699)throw new TypeError("Invalid statusCode: "+t.statusCode);const i=t.reasonPhrase?t.reasonPhrase:E(t.statusCode);let r="SIP/2.0 "+t.statusCode+" "+i+s;t.statusCode>=100&&t.statusCode,t.statusCode;const n="From: "+e.getHeader("From")+s,o="Call-ID: "+e.callId+s,a="CSeq: "+e.cseq+" "+e.method+s,c=e.getHeaders("via").reduce(((e,t)=>e+"Via: "+t+s),"");let h="To: "+e.getHeader("to");if(t.statusCode>100&&!e.parseHeader("to").hasParam("tag")){let e=t.toTag;e||(e=$()),h+=";tag="+e}h+=s;let d="";t.supported&&(d="Supported: "+t.supported.join(", ")+s);let l="";t.userAgent&&(l="User-Agent: "+t.userAgent+s);let g="";return t.extraHeaders&&(g=t.extraHeaders.reduce(((e,t)=>e+t.trim()+s),"")),r+=c,r+=n,r+=h,r+=a,r+=o,r+=d,r+=l,r+=g,t.body?(r+="Content-Type: "+t.body.contentType+s,r+="Content-Length: "+C(t.body.content)+s+s,r+=t.body.content):r+="Content-Length: 0\r\n\r\n",{message:r}}class be extends n{constructor(e){super(e||"Unspecified transport error.")}}class Te{constructor(e,t,s,i,r){this._transport=e,this._user=t,this._id=s,this._state=i,this.listeners=new Array,this.logger=t.loggerFactory.getLogger(r,s),this.logger.debug(`Constructing ${this.typeToString()} with id ${this.id}.`)}dispose(){this.logger.debug(`Destroyed ${this.typeToString()} with id ${this.id}.`)}get id(){return this._id}get kind(){throw new Error("Invalid kind.")}get state(){return this._state}get transport(){return this._transport}addStateChangeListener(e,t){const s=()=>{this.removeStateChangeListener(s),e()};!0===(null==t?void 0:t.once)?this.listeners.push(s):this.listeners.push(e)}notifyStateChangeListeners(){this.listeners.slice().forEach((e=>e()))}removeStateChangeListener(e){this.listeners=this.listeners.filter((t=>t!==e))}logTransportError(e,t){this.logger.error(e.message),this.logger.error(`Transport error occurred in ${this.typeToString()} with id ${this.id}.`),this.logger.error(t)}send(e){return this.transport.send(e).catch((e=>{if(e instanceof be)throw this.onTransportError(e),e;let t;throw t=e&&"string"==typeof e.message?new be(e.message):new be,this.onTransportError(t),t}))}setState(e){this.logger.debug(`State change to "${e}" on ${this.typeToString()} with id ${this.id}.`),this._state=e,this._user.onStateChange&&this._user.onStateChange(e),this.notifyStateChangeListeners()}typeToString(){return"UnknownType"}}class ye extends Te{constructor(e,t,s,i,r){super(t,s,ye.makeId(e),i,r),this._request=e,this.user=s,e.setViaHeader(this.id,t.protocol)}static makeId(e){if("CANCEL"===e.method){if(!e.branch)throw new Error("Outgoing CANCEL request without a branch.");return e.branch}return"z9hG4bK"+Math.floor(1e7*Math.random())}get request(){return this._request}onRequestTimeout(){this.user.onRequestTimeout&&this.user.onRequestTimeout()}}!function(e){e.Accepted="Accepted",e.Calling="Calling",e.Completed="Completed",e.Confirmed="Confirmed",e.Proceeding="Proceeding",e.Terminated="Terminated",e.Trying="Trying"}(ae||(ae={}));class Se extends ye{constructor(e,t,s){super(e,t,s,ae.Trying,"sip.transaction.nict"),this.F=setTimeout((()=>this.timerF()),F.TIMER_F),this.send(e.toString()).catch((e=>{this.logTransportError(e,"Failed to send initial outgoing request.")}))}dispose(){this.F&&(clearTimeout(this.F),this.F=void 0),this.K&&(clearTimeout(this.K),this.K=void 0),super.dispose()}get kind(){return"nict"}receiveResponse(e){const t=e.statusCode;if(!t||t<100||t>699)throw new Error(`Invalid status code ${t}`);switch(this.state){case ae.Trying:if(t>=100&&t<=199)return this.stateTransition(ae.Proceeding),void(this.user.receiveResponse&&this.user.receiveResponse(e));if(t>=200&&t<=699)return this.stateTransition(ae.Completed),408===t?void this.onRequestTimeout():void(this.user.receiveResponse&&this.user.receiveResponse(e));break;case ae.Proceeding:if(t>=100&&t<=199&&this.user.receiveResponse)return this.user.receiveResponse(e);if(t>=200&&t<=699)return this.stateTransition(ae.Completed),408===t?void this.onRequestTimeout():void(this.user.receiveResponse&&this.user.receiveResponse(e));break;case ae.Completed:case ae.Terminated:return;default:throw new Error(`Invalid state ${this.state}`)}const s=`Non-INVITE client transaction received unexpected ${t} response while in state ${this.state}.`;this.logger.warn(s)}onTransportError(e){this.user.onTransportError&&this.user.onTransportError(e),this.stateTransition(ae.Terminated,!0)}typeToString(){return"non-INVITE client transaction"}stateTransition(e,t=!1){const s=()=>{throw new Error(`Invalid state transition from ${this.state} to ${e}`)};switch(e){case ae.Trying:s();break;case ae.Proceeding:this.state!==ae.Trying&&s();break;case ae.Completed:this.state!==ae.Trying&&this.state!==ae.Proceeding&&s();break;case ae.Terminated:this.state!==ae.Trying&&this.state!==ae.Proceeding&&this.state!==ae.Completed&&(t||s());break;default:s()}e===ae.Completed&&(this.F&&(clearTimeout(this.F),this.F=void 0),this.K=setTimeout((()=>this.timerK()),F.TIMER_K)),e===ae.Terminated&&this.dispose(),this.setState(e)}timerF(){this.logger.debug(`Timer F expired for non-INVITE client transaction ${this.id}.`),this.state!==ae.Trying&&this.state!==ae.Proceeding||(this.onRequestTimeout(),this.stateTransition(ae.Terminated))}timerK(){this.state===ae.Completed&&this.stateTransition(ae.Terminated)}}class Re extends Te{constructor(e,t,s,i,r){super(t,s,e.viaBranch,i,r),this._request=e,this.user=s}get request(){return this._request}}class Ee extends Re{constructor(e,t,s){super(e,t,s,ae.Proceeding,"sip.transaction.ist")}dispose(){this.stopProgressExtensionTimer(),this.H&&(clearTimeout(this.H),this.H=void 0),this.I&&(clearTimeout(this.I),this.I=void 0),this.L&&(clearTimeout(this.L),this.L=void 0),super.dispose()}get kind(){return"ist"}receiveRequest(e){switch(this.state){case ae.Proceeding:if(e.method===L.INVITE)return void(this.lastProvisionalResponse&&this.send(this.lastProvisionalResponse).catch((e=>{this.logTransportError(e,"Failed to send retransmission of provisional response.")})));break;case ae.Accepted:if(e.method===L.INVITE)return;break;case ae.Completed:if(e.method===L.INVITE){if(!this.lastFinalResponse)throw new Error("Last final response undefined.");return void this.send(this.lastFinalResponse).catch((e=>{this.logTransportError(e,"Failed to send retransmission of final response.")}))}if(e.method===L.ACK)return void this.stateTransition(ae.Confirmed);break;case ae.Confirmed:case ae.Terminated:if(e.method===L.INVITE||e.method===L.ACK)return;break;default:throw new Error(`Invalid state ${this.state}`)}const t=`INVITE server transaction received unexpected ${e.method} request while in state ${this.state}.`;this.logger.warn(t)}receiveResponse(e,t){if(e<100||e>699)throw new Error(`Invalid status code ${e}`);switch(this.state){case ae.Proceeding:if(e>=100&&e<=199)return this.lastProvisionalResponse=t,e>100&&this.startProgressExtensionTimer(),void this.send(t).catch((e=>{this.logTransportError(e,"Failed to send 1xx response.")}));if(e>=200&&e<=299)return this.lastFinalResponse=t,this.stateTransition(ae.Accepted),void this.send(t).catch((e=>{this.logTransportError(e,"Failed to send 2xx response.")}));if(e>=300&&e<=699)return this.lastFinalResponse=t,this.stateTransition(ae.Completed),void this.send(t).catch((e=>{this.logTransportError(e,"Failed to send non-2xx final response.")}));break;case ae.Accepted:if(e>=200&&e<=299)return void this.send(t).catch((e=>{this.logTransportError(e,"Failed to send 2xx response.")}));break;case ae.Completed:case ae.Confirmed:case ae.Terminated:break;default:throw new Error(`Invalid state ${this.state}`)}const s=`INVITE server transaction received unexpected ${e} response from TU while in state ${this.state}.`;throw this.logger.error(s),new Error(s)}retransmitAcceptedResponse(){this.state===ae.Accepted&&this.lastFinalResponse&&this.send(this.lastFinalResponse).catch((e=>{this.logTransportError(e,"Failed to send 2xx response.")}))}onTransportError(e){this.user.onTransportError&&this.user.onTransportError(e)}typeToString(){return"INVITE server transaction"}stateTransition(e){const t=()=>{throw new Error(`Invalid state transition from ${this.state} to ${e}`)};switch(e){case ae.Proceeding:t();break;case ae.Accepted:case ae.Completed:this.state!==ae.Proceeding&&t();break;case ae.Confirmed:this.state!==ae.Completed&&t();break;case ae.Terminated:this.state!==ae.Accepted&&this.state!==ae.Completed&&this.state!==ae.Confirmed&&t();break;default:t()}this.stopProgressExtensionTimer(),e===ae.Accepted&&(this.L=setTimeout((()=>this.timerL()),F.TIMER_L)),e===ae.Completed&&(this.H=setTimeout((()=>this.timerH()),F.TIMER_H)),e===ae.Confirmed&&(this.I=setTimeout((()=>this.timerI()),F.TIMER_I)),e===ae.Terminated&&this.dispose(),this.setState(e)}startProgressExtensionTimer(){void 0===this.progressExtensionTimer&&(this.progressExtensionTimer=setInterval((()=>{if(this.logger.debug(`Progress extension timer expired for INVITE server transaction ${this.id}.`),!this.lastProvisionalResponse)throw new Error("Last provisional response undefined.");this.send(this.lastProvisionalResponse).catch((e=>{this.logTransportError(e,"Failed to send retransmission of provisional response.")}))}),F.PROVISIONAL_RESPONSE_INTERVAL))}stopProgressExtensionTimer(){void 0!==this.progressExtensionTimer&&(clearInterval(this.progressExtensionTimer),this.progressExtensionTimer=void 0)}timerG(){}timerH(){this.logger.debug(`Timer H expired for INVITE server transaction ${this.id}.`),this.state===ae.Completed&&(this.logger.warn("ACK to negative final response was never received, terminating transaction."),this.stateTransition(ae.Terminated))}timerI(){this.logger.debug(`Timer I expired for INVITE server transaction ${this.id}.`),this.stateTransition(ae.Terminated)}timerL(){this.logger.debug(`Timer L expired for INVITE server transaction ${this.id}.`),this.state===ae.Accepted&&this.stateTransition(ae.Terminated)}}class $e{constructor(e,t){this.core=e,this.dialogState=t,this.core.dialogs.set(this.id,this)}static initialDialogStateForUserAgentClient(e,t){const s=t.getHeaders("record-route").reverse(),i=t.parseHeader("contact");if(!i)throw new Error("Contact undefined.");if(!(i instanceof m))throw new Error("Contact not instance of NameAddrHeader.");const r=i.uri,n=e.cseq,o=e.callId,a=e.fromTag,c=t.toTag;if(!o)throw new Error("Call id undefined.");if(!a)throw new Error("From tag undefined.");if(!c)throw new Error("To tag undefined.");if(!e.from)throw new Error("From undefined.");if(!e.to)throw new Error("To undefined.");const h=e.from.uri,d=e.to.uri;if(!t.statusCode)throw new Error("Incoming response status code undefined.");return{id:o+a+c,early:t.statusCode<200,callId:o,localTag:a,remoteTag:c,localSequenceNumber:n,remoteSequenceNumber:undefined,localURI:h,remoteURI:d,remoteTarget:r,routeSet:s,secure:!1}}static initialDialogStateForUserAgentServer(e,t,s=!1){const i=e.getHeaders("record-route"),r=e.parseHeader("contact");if(!r)throw new Error("Contact undefined.");if(!(r instanceof m))throw new Error("Contact not instance of NameAddrHeader.");const n=r.uri,o=e.cseq,a=e.callId,c=t,h=e.fromTag,d=e.from.uri;return{id:a+c+h,early:s,callId:a,localTag:c,remoteTag:h,localSequenceNumber:undefined,remoteSequenceNumber:o,localURI:e.to.uri,remoteURI:d,remoteTarget:n,routeSet:i,secure:!1}}dispose(){this.core.dialogs.delete(this.id)}get id(){return this.dialogState.id}get early(){return this.dialogState.early}get callId(){return this.dialogState.callId}get localTag(){return this.dialogState.localTag}get remoteTag(){return this.dialogState.remoteTag}get localSequenceNumber(){return this.dialogState.localSequenceNumber}get remoteSequenceNumber(){return this.dialogState.remoteSequenceNumber}get localURI(){return this.dialogState.localURI}get remoteURI(){return this.dialogState.remoteURI}get remoteTarget(){return this.dialogState.remoteTarget}get routeSet(){return this.dialogState.routeSet}get secure(){return this.dialogState.secure}get userAgentCore(){return this.core}confirm(){this.dialogState.early=!1}receiveRequest(e){if(e.method!==L.ACK){if(this.remoteSequenceNumber){if(e.cseq<=this.remoteSequenceNumber)throw new Error("Out of sequence in dialog request. Did you forget to call sequenceGuard()?");this.dialogState.remoteSequenceNumber=e.cseq}this.remoteSequenceNumber||(this.dialogState.remoteSequenceNumber=e.cseq)}}recomputeRouteSet(e){this.dialogState.routeSet=e.getHeaders("record-route").reverse()}createOutgoingRequestMessage(e,t){const s=this.remoteURI,i=this.remoteTag,r=this.localURI,n=this.localTag,o=this.callId;let a;a=t&&t.cseq?t.cseq:this.dialogState.localSequenceNumber?this.dialogState.localSequenceNumber+=1:this.dialogState.localSequenceNumber=1;const c=this.remoteTarget,h=this.routeSet,d=t&&t.extraHeaders,l=t&&t.body;return this.userAgentCore.makeOutgoingRequestMessage(e,c,r,s,{callId:o,cseq:a,fromTag:n,toTag:i,routeSet:h},d,l)}incrementLocalSequenceNumber(){if(!this.dialogState.localSequenceNumber)throw new Error("Local sequence number undefined.");this.dialogState.localSequenceNumber+=1}sequenceGuard(e){return e.method===L.ACK||(!(this.remoteSequenceNumber&&e.cseq<=this.remoteSequenceNumber)||(this.core.replyStateless(e,{statusCode:500}),!1))}}class Ie extends ye{constructor(e,t,s){super(e,t,s,ae.Calling,"sip.transaction.ict"),this.ackRetransmissionCache=new Map,this.B=setTimeout((()=>this.timerB()),F.TIMER_B),this.send(e.toString()).catch((e=>{this.logTransportError(e,"Failed to send initial outgoing request.")}))}dispose(){this.B&&(clearTimeout(this.B),this.B=void 0),this.D&&(clearTimeout(this.D),this.D=void 0),this.M&&(clearTimeout(this.M),this.M=void 0),super.dispose()}get kind(){return"ict"}ackResponse(e){const t=e.toTag;if(!t)throw new Error("To tag undefined.");const s="z9hG4bK"+Math.floor(1e7*Math.random());e.setViaHeader(s,this.transport.protocol),this.ackRetransmissionCache.set(t,e),this.send(e.toString()).catch((e=>{this.logTransportError(e,"Failed to send ACK to 2xx response.")}))}receiveResponse(e){const t=e.statusCode;if(!t||t<100||t>699)throw new Error(`Invalid status code ${t}`);switch(this.state){case ae.Calling:if(t>=100&&t<=199)return this.stateTransition(ae.Proceeding),void(this.user.receiveResponse&&this.user.receiveResponse(e));if(t>=200&&t<=299)return this.ackRetransmissionCache.set(e.toTag,void 0),this.stateTransition(ae.Accepted),void(this.user.receiveResponse&&this.user.receiveResponse(e));if(t>=300&&t<=699)return this.stateTransition(ae.Completed),this.ack(e),void(this.user.receiveResponse&&this.user.receiveResponse(e));break;case ae.Proceeding:if(t>=100&&t<=199)return void(this.user.receiveResponse&&this.user.receiveResponse(e));if(t>=200&&t<=299)return this.ackRetransmissionCache.set(e.toTag,void 0),this.stateTransition(ae.Accepted),void(this.user.receiveResponse&&this.user.receiveResponse(e));if(t>=300&&t<=699)return this.stateTransition(ae.Completed),this.ack(e),void(this.user.receiveResponse&&this.user.receiveResponse(e));break;case ae.Accepted:if(t>=200&&t<=299){if(!this.ackRetransmissionCache.has(e.toTag))return this.ackRetransmissionCache.set(e.toTag,void 0),void(this.user.receiveResponse&&this.user.receiveResponse(e));const t=this.ackRetransmissionCache.get(e.toTag);return t?void this.send(t.toString()).catch((e=>{this.logTransportError(e,"Failed to send retransmission of ACK to 2xx response.")})):void 0}break;case ae.Completed:if(t>=300&&t<=699)return void this.ack(e);break;case ae.Terminated:break;default:throw new Error(`Invalid state ${this.state}`)}const s=`Received unexpected ${t} response while in state ${this.state}.`;this.logger.warn(s)}onTransportError(e){this.user.onTransportError&&this.user.onTransportError(e),this.stateTransition(ae.Terminated,!0)}typeToString(){return"INVITE client transaction"}ack(e){const t=this.request.ruri,s=this.request.callId,i=this.request.cseq,r=this.request.getHeader("from"),n=e.getHeader("to"),o=this.request.getHeader("via"),a=this.request.getHeader("route");if(!r)throw new Error("From undefined.");if(!n)throw new Error("To undefined.");if(!o)throw new Error("Via undefined.");let c=`ACK ${t} SIP/2.0\r\n`;a&&(c+=`Route: ${a}\r\n`),c+=`Via: ${o}\r\n`,c+=`To: ${n}\r\n`,c+=`From: ${r}\r\n`,c+=`Call-ID: ${s}\r\n`,c+=`CSeq: ${i} ACK\r\n`,c+="Max-Forwards: 70\r\n",c+="Content-Length: 0\r\n\r\n",this.send(c).catch((e=>{this.logTransportError(e,"Failed to send ACK to non-2xx response.")}))}stateTransition(e,t=!1){const s=()=>{throw new Error(`Invalid state transition from ${this.state} to ${e}`)};switch(e){case ae.Calling:s();break;case ae.Proceeding:this.state!==ae.Calling&&s();break;case ae.Accepted:case ae.Completed:this.state!==ae.Calling&&this.state!==ae.Proceeding&&s();break;case ae.Terminated:this.state!==ae.Calling&&this.state!==ae.Accepted&&this.state!==ae.Completed&&(t||s());break;default:s()}this.B&&(clearTimeout(this.B),this.B=void 0),ae.Proceeding,e===ae.Completed&&(this.D=setTimeout((()=>this.timerD()),F.TIMER_D)),e===ae.Accepted&&(this.M=setTimeout((()=>this.timerM()),F.TIMER_M)),e===ae.Terminated&&this.dispose(),this.setState(e)}timerA(){}timerB(){this.logger.debug(`Timer B expired for INVITE client transaction ${this.id}.`),this.state===ae.Calling&&(this.onRequestTimeout(),this.stateTransition(ae.Terminated))}timerD(){this.logger.debug(`Timer D expired for INVITE client transaction ${this.id}.`),this.state===ae.Completed&&this.stateTransition(ae.Terminated)}timerM(){this.logger.debug(`Timer M expired for INVITE client transaction ${this.id}.`),this.state===ae.Accepted&&this.stateTransition(ae.Terminated)}}class Ce{constructor(e,t,s,i){this.transactionConstructor=e,this.core=t,this.message=s,this.delegate=i,this.challenged=!1,this.stale=!1,this.logger=this.loggerFactory.getLogger("sip.user-agent-client"),this.init()}dispose(){this.transaction.dispose()}get loggerFactory(){return this.core.loggerFactory}get transaction(){if(!this._transaction)throw new Error("Transaction undefined.");return this._transaction}cancel(e,t={}){if(!this.transaction)throw new Error("Transaction undefined.");if(!this.message.to)throw new Error("To undefined.");if(!this.message.from)throw new Error("From undefined.");const s=this.core.makeOutgoingRequestMessage(L.CANCEL,this.message.ruri,this.message.from.uri,this.message.to.uri,{toTag:this.message.toTag,fromTag:this.message.fromTag,callId:this.message.callId,cseq:this.message.cseq},t.extraHeaders);return s.branch=this.message.branch,this.message.headers.Route&&(s.headers.Route=this.message.headers.Route),e&&s.setHeader("Reason",e),this.transaction.state===ae.Proceeding?new Ce(Se,this.core,s):this.transaction.addStateChangeListener((()=>{this.transaction&&this.transaction.state===ae.Proceeding&&new Ce(Se,this.core,s)}),{once:!0}),s}authenticationGuard(e,t){const s=e.statusCode;if(!s)throw new Error("Response status code undefined.");if(401!==s&&407!==s)return!0;let i,r;if(401===s?(i=e.parseHeader("www-authenticate"),r="authorization"):(i=e.parseHeader("proxy-authenticate"),r="proxy-authorization"),!i)return this.logger.warn(s+" with wrong or missing challenge, cannot authenticate"),!0;if(this.challenged&&(this.stale||!0!==i.stale))return this.logger.warn(s+" apparently in authentication loop, cannot authenticate"),!0;if(!this.credentials&&(this.credentials=this.core.configuration.authenticationFactory(),!this.credentials))return this.logger.warn("Unable to obtain credentials, cannot authenticate"),!0;if(!this.credentials.authenticate(this.message,i))return!0;this.challenged=!0,i.stale&&(this.stale=!0);let n=this.message.cseq+=1;return t&&t.localSequenceNumber&&(t.incrementLocalSequenceNumber(),n=this.message.cseq=t.localSequenceNumber),this.message.setHeader("cseq",n+" "+this.message.method),this.message.setHeader(r,this.credentials.toString()),this.init(),!1}onRequestTimeout(){this.logger.warn("User agent client request timed out. Generating internal 408 Request Timeout.");const e=new H;e.statusCode=408,e.reasonPhrase="Request Timeout",this.receiveResponse(e)}onTransportError(e){this.logger.error(e.message),this.logger.error("User agent client request transport error. Generating internal 503 Service Unavailable.");const t=new H;t.statusCode=503,t.reasonPhrase="Service Unavailable",this.receiveResponse(t)}receiveResponse(e){if(!this.authenticationGuard(e))return;const t=e.statusCode?e.statusCode.toString():"";if(!t)throw new Error("Response status code undefined.");switch(!0){case/^100$/.test(t):this.delegate&&this.delegate.onTrying&&this.delegate.onTrying({message:e});break;case/^1[0-9]{2}$/.test(t):this.delegate&&this.delegate.onProgress&&this.delegate.onProgress({message:e});break;case/^2[0-9]{2}$/.test(t):this.delegate&&this.delegate.onAccept&&this.delegate.onAccept({message:e});break;case/^3[0-9]{2}$/.test(t):this.delegate&&this.delegate.onRedirect&&this.delegate.onRedirect({message:e});break;case/^[4-6][0-9]{2}$/.test(t):this.delegate&&this.delegate.onReject&&this.delegate.onReject({message:e});break;default:throw new Error(`Invalid status code ${t}`)}}init(){const e={loggerFactory:this.loggerFactory,onRequestTimeout:()=>this.onRequestTimeout(),onStateChange:e=>{e===ae.Terminated&&(this.core.userAgentClients.delete(s),t===this._transaction&&this.dispose())},onTransportError:e=>this.onTransportError(e),receiveResponse:e=>this.receiveResponse(e)},t=new this.transactionConstructor(this.message,this.core.transport,e);this._transaction=t;const s=t.id+t.request.method;this.core.userAgentClients.set(s,this)}}class Ae extends Ce{constructor(e,t,s){const i=e.createOutgoingRequestMessage(L.BYE,s);super(Se,e.userAgentCore,i,t),e.dispose()}}class De extends Re{constructor(e,t,s){super(e,t,s,ae.Trying,"sip.transaction.nist")}dispose(){this.J&&(clearTimeout(this.J),this.J=void 0),super.dispose()}get kind(){return"nist"}receiveRequest(e){switch(this.state){case ae.Trying:break;case ae.Proceeding:if(!this.lastResponse)throw new Error("Last response undefined.");this.send(this.lastResponse).catch((e=>{this.logTransportError(e,"Failed to send retransmission of provisional response.")}));break;case ae.Completed:if(!this.lastResponse)throw new Error("Last response undefined.");this.send(this.lastResponse).catch((e=>{this.logTransportError(e,"Failed to send retransmission of final response.")}));break;case ae.Terminated:break;default:throw new Error(`Invalid state ${this.state}`)}}receiveResponse(e,t){if(e<100||e>699)throw new Error(`Invalid status code ${e}`);if(e>100&&e<=199)throw new Error("Provisional response other than 100 not allowed.");switch(this.state){case ae.Trying:if(this.lastResponse=t,e>=100&&e<200)return this.stateTransition(ae.Proceeding),void this.send(t).catch((e=>{this.logTransportError(e,"Failed to send provisional response.")}));if(e>=200&&e<=699)return this.stateTransition(ae.Completed),void this.send(t).catch((e=>{this.logTransportError(e,"Failed to send final response.")}));break;case ae.Proceeding:if(this.lastResponse=t,e>=200&&e<=699)return this.stateTransition(ae.Completed),void this.send(t).catch((e=>{this.logTransportError(e,"Failed to send final response.")}));break;case ae.Completed:return;case ae.Terminated:break;default:throw new Error(`Invalid state ${this.state}`)}const s=`Non-INVITE server transaction received unexpected ${e} response from TU while in state ${this.state}.`;throw this.logger.error(s),new Error(s)}onTransportError(e){this.user.onTransportError&&this.user.onTransportError(e),this.stateTransition(ae.Terminated,!0)}typeToString(){return"non-INVITE server transaction"}stateTransition(e,t=!1){const s=()=>{throw new Error(`Invalid state transition from ${this.state} to ${e}`)};switch(e){case ae.Trying:s();break;case ae.Proceeding:this.state!==ae.Trying&&s();break;case ae.Completed:this.state!==ae.Trying&&this.state!==ae.Proceeding&&s();break;case ae.Terminated:this.state!==ae.Proceeding&&this.state!==ae.Completed&&(t||s());break;default:s()}e===ae.Completed&&(this.J=setTimeout((()=>this.timerJ()),F.TIMER_J)),e===ae.Terminated&&this.dispose(),this.setState(e)}timerJ(){this.logger.debug(`Timer J expired for NON-INVITE server transaction ${this.id}.`),this.state===ae.Completed&&this.stateTransition(ae.Terminated)}}class He{constructor(e,t,s,i){this.transactionConstructor=e,this.core=t,this.message=s,this.delegate=i,this.logger=this.loggerFactory.getLogger("sip.user-agent-server"),this.toTag=s.toTag?s.toTag:$(),this.init()}dispose(){this.transaction.dispose()}get loggerFactory(){return this.core.loggerFactory}get transaction(){if(!this._transaction)throw new Error("Transaction undefined.");return this._transaction}accept(e={statusCode:200}){if(!this.acceptable)throw new O(`${this.message.method} not acceptable in state ${this.transaction.state}.`);const t=e.statusCode;if(t<200||t>299)throw new TypeError(`Invalid statusCode: ${t}`);return this.reply(e)}progress(e={statusCode:180}){if(!this.progressable)throw new O(`${this.message.method} not progressable in state ${this.transaction.state}.`);const t=e.statusCode;if(t<101||t>199)throw new TypeError(`Invalid statusCode: ${t}`);return this.reply(e)}redirect(e,t={statusCode:302}){if(!this.redirectable)throw new O(`${this.message.method} not redirectable in state ${this.transaction.state}.`);const s=t.statusCode;if(s<300||s>399)throw new TypeError(`Invalid statusCode: ${s}`);const i=new Array;e.forEach((e=>i.push(`Contact: ${e.toString()}`))),t.extraHeaders=(t.extraHeaders||[]).concat(i);return this.reply(t)}reject(e={statusCode:480}){if(!this.rejectable)throw new O(`${this.message.method} not rejectable in state ${this.transaction.state}.`);const t=e.statusCode;if(t<400||t>699)throw new TypeError(`Invalid statusCode: ${t}`);return this.reply(e)}trying(e){if(!this.tryingable)throw new O(`${this.message.method} not tryingable in state ${this.transaction.state}.`);return this.reply({statusCode:100})}receiveCancel(e){this.delegate&&this.delegate.onCancel&&this.delegate.onCancel(e)}get acceptable(){if(this.transaction instanceof Ee)return this.transaction.state===ae.Proceeding||this.transaction.state===ae.Accepted;if(this.transaction instanceof De)return this.transaction.state===ae.Trying||this.transaction.state===ae.Proceeding;throw new Error("Unknown transaction type.")}get progressable(){if(this.transaction instanceof Ee)return this.transaction.state===ae.Proceeding;if(this.transaction instanceof De)return!1;throw new Error("Unknown transaction type.")}get redirectable(){if(this.transaction instanceof Ee)return this.transaction.state===ae.Proceeding;if(this.transaction instanceof De)return this.transaction.state===ae.Trying||this.transaction.state===ae.Proceeding;throw new Error("Unknown transaction type.")}get rejectable(){if(this.transaction instanceof Ee)return this.transaction.state===ae.Proceeding;if(this.transaction instanceof De)return this.transaction.state===ae.Trying||this.transaction.state===ae.Proceeding;throw new Error("Unknown transaction type.")}get tryingable(){if(this.transaction instanceof Ee)return this.transaction.state===ae.Proceeding;if(this.transaction instanceof De)return this.transaction.state===ae.Trying;throw new Error("Unknown transaction type.")}reply(e){e.toTag||100===e.statusCode||(e.toTag=this.toTag),e.userAgent=e.userAgent||this.core.configuration.userAgentHeaderFieldValue,e.supported=e.supported||this.core.configuration.supportedOptionTagsResponse;const t=we(this.message,e);return this.transaction.receiveResponse(e.statusCode,t.message),t}init(){const e={loggerFactory:this.loggerFactory,onStateChange:e=>{e===ae.Terminated&&(this.core.userAgentServers.delete(s),this.dispose())},onTransportError:e=>{this.logger.error(e.message),this.delegate&&this.delegate.onTransportError?this.delegate.onTransportError(e):this.logger.error("User agent server response transport error.")}},t=new this.transactionConstructor(this.message,this.core.transport,e);this._transaction=t;const s=t.id;this.core.userAgentServers.set(t.id,this)}}class ke extends He{constructor(e,t,s){super(De,e.userAgentCore,t,s)}}class _e extends Ce{constructor(e,t,s){const i=e.createOutgoingRequestMessage(L.INFO,s);super(Se,e.userAgentCore,i,t)}}class Pe extends He{constructor(e,t,s){super(De,e.userAgentCore,t,s)}}class qe extends Ce{constructor(e,t,s){super(Se,e,t,s)}}class xe extends He{constructor(e,t,s){super(De,e,t,s)}}class Ne extends Ce{constructor(e,t,s){const i=e.createOutgoingRequestMessage(L.NOTIFY,s);super(Se,e.userAgentCore,i,t)}}class Me extends He{constructor(e,t,s){const i=void 0!==e.userAgentCore?e.userAgentCore:e;super(De,i,t,s)}}class Oe extends Ce{constructor(e,t,s){const i=e.createOutgoingRequestMessage(L.PRACK,s);super(Se,e.userAgentCore,i,t),e.signalingStateTransition(i)}}class Ue extends He{constructor(e,t,s){super(De,e.userAgentCore,t,s),e.signalingStateTransition(t),this.dialog=e}accept(e={statusCode:200}){return e.body&&this.dialog.signalingStateTransition(e.body),super.accept(e)}}class je extends Ce{constructor(e,t,s){const i=e.createOutgoingRequestMessage(L.INVITE,s);super(Ie,e.userAgentCore,i,t),this.delegate=t,e.signalingStateTransition(i),e.reinviteUserAgentClient=this,this.dialog=e}receiveResponse(e){if(!this.authenticationGuard(e,this.dialog))return;const t=e.statusCode?e.statusCode.toString():"";if(!t)throw new Error("Response status code undefined.");switch(!0){case/^100$/.test(t):this.delegate&&this.delegate.onTrying&&this.delegate.onTrying({message:e});break;case/^1[0-9]{2}$/.test(t):this.delegate&&this.delegate.onProgress&&this.delegate.onProgress({message:e,session:this.dialog,prack:e=>{throw new Error("Unimplemented.")}});break;case/^2[0-9]{2}$/.test(t):this.dialog.signalingStateTransition(e),this.delegate&&this.delegate.onAccept&&this.delegate.onAccept({message:e,session:this.dialog,ack:e=>this.dialog.ack(e)});break;case/^3[0-9]{2}$/.test(t):this.dialog.signalingStateRollback(),this.dialog.reinviteUserAgentClient=void 0,this.delegate&&this.delegate.onRedirect&&this.delegate.onRedirect({message:e});break;case/^[4-6][0-9]{2}$/.test(t):this.dialog.signalingStateRollback(),this.dialog.reinviteUserAgentClient=void 0,this.delegate&&this.delegate.onReject&&this.delegate.onReject({message:e});break;default:throw new Error(`Invalid status code ${t}`)}}}class Fe extends He{constructor(e,t,s){super(Ee,e.userAgentCore,t,s),e.reinviteUserAgentServer=this,this.dialog=e}accept(e={statusCode:200}){e.extraHeaders=e.extraHeaders||[],e.extraHeaders=e.extraHeaders.concat(this.dialog.routeSet.map((e=>`Record-Route: ${e}`)));const t=super.accept(e),s=this.dialog,i=Object.assign(Object.assign({},t),{session:s});return e.body&&this.dialog.signalingStateTransition(e.body),this.dialog.reConfirm(),i}progress(e={statusCode:180}){const t=super.progress(e),s=this.dialog,i=Object.assign(Object.assign({},t),{session:s});return e.body&&this.dialog.signalingStateTransition(e.body),i}redirect(e,t={statusCode:302}){throw this.dialog.signalingStateRollback(),this.dialog.reinviteUserAgentServer=void 0,new Error("Unimplemented.")}reject(e={statusCode:488}){return this.dialog.signalingStateRollback(),this.dialog.reinviteUserAgentServer=void 0,super.reject(e)}}class Le extends Ce{constructor(e,t,s){const i=e.createOutgoingRequestMessage(L.REFER,s);super(Se,e.userAgentCore,i,t)}}class Be extends He{constructor(e,t,s){const i=void 0!==e.userAgentCore?e.userAgentCore:e;super(De,i,t,s)}}class Ge extends $e{constructor(e,t,s,i){super(t,s),this.initialTransaction=e,this._signalingState=M.Initial,this.ackWait=!1,this.ackProcessing=!1,this.delegate=i,e instanceof Ee&&(this.ackWait=!0),this.early||this.start2xxRetransmissionTimer(),this.signalingStateTransition(e.request),this.logger=t.loggerFactory.getLogger("sip.invite-dialog"),this.logger.log(`INVITE dialog ${this.id} constructed`)}dispose(){super.dispose(),this._signalingState=M.Closed,this._offer=void 0,this._answer=void 0,this.invite2xxTimer&&(clearTimeout(this.invite2xxTimer),this.invite2xxTimer=void 0),this.logger.log(`INVITE dialog ${this.id} destroyed`)}get sessionState(){return this.early?N.Early:this.ackWait?N.AckWait:this._signalingState===M.Closed?N.Terminated:N.Confirmed}get signalingState(){return this._signalingState}get offer(){return this._offer}get answer(){return this._answer}confirm(){this.early&&this.start2xxRetransmissionTimer(),super.confirm()}reConfirm(){this.reinviteUserAgentServer&&this.startReInvite2xxRetransmissionTimer()}ack(e={}){let t;if(this.logger.log(`INVITE dialog ${this.id} sending ACK request`),this.reinviteUserAgentClient){if(!(this.reinviteUserAgentClient.transaction instanceof Ie))throw new Error("Transaction not instance of InviteClientTransaction.");t=this.reinviteUserAgentClient.transaction,this.reinviteUserAgentClient=void 0}else{if(!(this.initialTransaction instanceof Ie))throw new Error("Initial transaction not instance of InviteClientTransaction.");t=this.initialTransaction}const s=this.createOutgoingRequestMessage(L.ACK,{cseq:t.request.cseq,extraHeaders:e.extraHeaders,body:e.body});return t.ackResponse(s),this.signalingStateTransition(s),{message:s}}bye(e,t){if(this.logger.log(`INVITE dialog ${this.id} sending BYE request`),this.initialTransaction instanceof Ee){if(this.early)throw new Error("UAS MUST NOT send a BYE on early dialogs.");if(this.ackWait&&this.initialTransaction.state!==ae.Terminated)throw new Error("UAS MUST NOT send a BYE on a confirmed dialog until it has received an ACK for its 2xx response or until the server transaction times out.")}return new Ae(this,e,t)}info(e,t){if(this.logger.log(`INVITE dialog ${this.id} sending INFO request`),this.early)throw new Error("Dialog not confirmed.");return new _e(this,e,t)}invite(e,t){if(this.logger.log(`INVITE dialog ${this.id} sending INVITE request`),this.early)throw new Error("Dialog not confirmed.");if(this.reinviteUserAgentClient)throw new Error("There is an ongoing re-INVITE client transaction.");if(this.reinviteUserAgentServer)throw new Error("There is an ongoing re-INVITE server transaction.");return new je(this,e,t)}message(e,t){if(this.logger.log(`INVITE dialog ${this.id} sending MESSAGE request`),this.early)throw new Error("Dialog not confirmed.");const s=this.createOutgoingRequestMessage(L.MESSAGE,t);return new qe(this.core,s,e)}notify(e,t){if(this.logger.log(`INVITE dialog ${this.id} sending NOTIFY request`),this.early)throw new Error("Dialog not confirmed.");return new Ne(this,e,t)}prack(e,t){return this.logger.log(`INVITE dialog ${this.id} sending PRACK request`),new Oe(this,e,t)}refer(e,t){if(this.logger.log(`INVITE dialog ${this.id} sending REFER request`),this.early)throw new Error("Dialog not confirmed.");return new Le(this,e,t)}receiveRequest(e){if(this.logger.log(`INVITE dialog ${this.id} received ${e.method} request`),e.method!==L.ACK)if(this.sequenceGuard(e)){if(super.receiveRequest(e),e.method===L.INVITE){const t=()=>{const e=this.ackWait?"waiting for initial ACK":"processing initial ACK";this.logger.warn(`INVITE dialog ${this.id} received re-INVITE while ${e}`);let t="RFC 5407 suggests the following to avoid this race condition... ";t+=" Note: Implementation issues are outside the scope of this document,",t+=" but the following tip is provided for avoiding race conditions of",t+=" this type.  The caller can delay sending re-INVITE F6 for some period",t+=" of time (2 seconds, perhaps), after which the caller can reasonably",t+=" assume that its ACK has been received.  Implementors can decouple the",t+=" actions of the user (e.g., pressing the hold button) from the actions",t+=" of the protocol (the sending of re-INVITE F6), so that the UA can",t+=" behave like this.  In this case, it is the implementor's choice as to",t+=" how long to wait.  In most cases, such an implementation may be",t+=" useful to prevent the type of race condition shown in this section.",t+=" This document expresses no preference about whether or not they",t+=" should wait for an ACK to be delivered.  After considering the impact",t+=" on user experience, implementors should decide whether or not to wait",t+=" for a while, because the user experience depends on the",t+=" implementation and has no direct bearing on protocol behavior.",this.logger.warn("RFC 5407 suggests the following to avoid this race condition...  Note: Implementation issues are outside the scope of this document, but the following tip is provided for avoiding race conditions of this type.  The caller can delay sending re-INVITE F6 for some period of time (2 seconds, perhaps), after which the caller can reasonably assume that its ACK has been received.  Implementors can decouple the actions of the user (e.g., pressing the hold button) from the actions of the protocol (the sending of re-INVITE F6), so that the UA can behave like this.  In this case, it is the implementor's choice as to how long to wait.  In most cases, such an implementation may be useful to prevent the type of race condition shown in this section. This document expresses no preference about whether or not they should wait for an ACK to be delivered.  After considering the impact on user experience, implementors should decide whether or not to wait for a while, because the user experience depends on the implementation and has no direct bearing on protocol behavior.")},s=[`Retry-After: ${Math.floor(10*Math.random())+1}`];if(this.ackProcessing)return this.core.replyStateless(e,{statusCode:500,extraHeaders:s}),void t();if(this.ackWait&&this.signalingState!==M.Stable)return this.core.replyStateless(e,{statusCode:500,extraHeaders:s}),void t();if(this.reinviteUserAgentServer)return void this.core.replyStateless(e,{statusCode:500,extraHeaders:s});if(this.reinviteUserAgentClient)return void this.core.replyStateless(e,{statusCode:491})}if(e.method===L.INVITE){const t=e.parseHeader("contact");if(!t)throw new Error("Contact undefined.");if(!(t instanceof m))throw new Error("Contact not instance of NameAddrHeader.");this.dialogState.remoteTarget=t.uri}switch(e.method){case L.BYE:{const t=new ke(this,e);this.delegate&&this.delegate.onBye?this.delegate.onBye(t):t.accept(),this.dispose()}break;case L.INFO:{const t=new Pe(this,e);this.delegate&&this.delegate.onInfo?this.delegate.onInfo(t):t.reject({statusCode:469,extraHeaders:["Recv-Info:"]})}break;case L.INVITE:{const t=new Fe(this,e);this.signalingStateTransition(e),this.delegate&&this.delegate.onInvite?this.delegate.onInvite(t):t.reject({statusCode:488})}break;case L.MESSAGE:{const t=new xe(this.core,e);this.delegate&&this.delegate.onMessage?this.delegate.onMessage(t):t.accept()}break;case L.NOTIFY:{const t=new Me(this,e);this.delegate&&this.delegate.onNotify?this.delegate.onNotify(t):t.accept()}break;case L.PRACK:{const t=new Ue(this,e);this.delegate&&this.delegate.onPrack?this.delegate.onPrack(t):t.accept()}break;case L.REFER:{const t=new Be(this,e);this.delegate&&this.delegate.onRefer?this.delegate.onRefer(t):t.reject()}break;default:this.logger.log(`INVITE dialog ${this.id} received unimplemented ${e.method} request`),this.core.replyStateless(e,{statusCode:501})}}else this.logger.log(`INVITE dialog ${this.id} rejected out of order ${e.method} request.`);else{if(this.ackWait){if(this.initialTransaction instanceof Ie)return void this.logger.warn(`INVITE dialog ${this.id} received unexpected ${e.method} request, dropping.`);if(this.initialTransaction.request.cseq!==e.cseq)return void this.logger.warn(`INVITE dialog ${this.id} received unexpected ${e.method} request, dropping.`);this.ackWait=!1}else{if(!this.reinviteUserAgentServer)return void this.logger.warn(`INVITE dialog ${this.id} received unexpected ${e.method} request, dropping.`);if(this.reinviteUserAgentServer.transaction.request.cseq!==e.cseq)return void this.logger.warn(`INVITE dialog ${this.id} received unexpected ${e.method} request, dropping.`);this.reinviteUserAgentServer=void 0}if(this.signalingStateTransition(e),this.delegate&&this.delegate.onAck){const t=this.delegate.onAck({message:e});t instanceof Promise&&(this.ackProcessing=!0,t.then((()=>this.ackProcessing=!1)).catch((()=>this.ackProcessing=!1)))}}}reliableSequenceGuard(e){const t=e.statusCode;if(!t)throw new Error("Status code undefined");if(t>100&&t<200){const t=e.getHeader("require"),s=e.getHeader("rseq"),i=t&&t.includes("100rel")&&s?Number(s):void 0;if(i){if(this.rseq&&this.rseq+1!==i)return!1;this.rseq=this.rseq?this.rseq+1:i}}return!0}signalingStateRollback(){this._signalingState!==M.HaveLocalOffer&&this.signalingState!==M.HaveRemoteOffer||this._rollbackOffer&&this._rollbackAnswer&&(this._signalingState=M.Stable,this._offer=this._rollbackOffer,this._answer=this._rollbackAnswer)}signalingStateTransition(e){const t=x(e);if(t&&"session"===t.contentDisposition){if(this._signalingState===M.Stable&&(this._rollbackOffer=this._offer,this._rollbackAnswer=this._answer),e instanceof D)switch(this._signalingState){case M.Initial:case M.Stable:this._signalingState=M.HaveRemoteOffer,this._offer=t,this._answer=void 0;break;case M.HaveLocalOffer:this._signalingState=M.Stable,this._answer=t;break;case M.HaveRemoteOffer:case M.Closed:break;default:throw new Error("Unexpected signaling state.")}if(e instanceof H)switch(this._signalingState){case M.Initial:case M.Stable:this._signalingState=M.HaveRemoteOffer,this._offer=t,this._answer=void 0;break;case M.HaveLocalOffer:this._signalingState=M.Stable,this._answer=t;break;case M.HaveRemoteOffer:case M.Closed:break;default:throw new Error("Unexpected signaling state.")}if(e instanceof k)switch(this._signalingState){case M.Initial:case M.Stable:this._signalingState=M.HaveLocalOffer,this._offer=t,this._answer=void 0;break;case M.HaveLocalOffer:break;case M.HaveRemoteOffer:this._signalingState=M.Stable,this._answer=t;break;case M.Closed:break;default:throw new Error("Unexpected signaling state.")}if(q(e))switch(this._signalingState){case M.Initial:case M.Stable:this._signalingState=M.HaveLocalOffer,this._offer=t,this._answer=void 0;break;case M.HaveLocalOffer:break;case M.HaveRemoteOffer:this._signalingState=M.Stable,this._answer=t;break;case M.Closed:break;default:throw new Error("Unexpected signaling state.")}}}start2xxRetransmissionTimer(){if(this.initialTransaction instanceof Ee){const e=this.initialTransaction;let t=F.T1;const s=()=>{this.ackWait?(this.logger.log("No ACK for 2xx response received, attempting retransmission"),e.retransmitAcceptedResponse(),t=Math.min(2*t,F.T2),this.invite2xxTimer=setTimeout(s,t)):this.invite2xxTimer=void 0};this.invite2xxTimer=setTimeout(s,t);const i=()=>{e.state===ae.Terminated&&(e.removeStateChangeListener(i),this.invite2xxTimer&&(clearTimeout(this.invite2xxTimer),this.invite2xxTimer=void 0),this.ackWait&&(this.delegate&&this.delegate.onAckTimeout?this.delegate.onAckTimeout():this.bye()))};e.addStateChangeListener(i)}}startReInvite2xxRetransmissionTimer(){if(this.reinviteUserAgentServer&&this.reinviteUserAgentServer.transaction instanceof Ee){const e=this.reinviteUserAgentServer.transaction;let t=F.T1;const s=()=>{this.reinviteUserAgentServer?(this.logger.log("No ACK for 2xx response received, attempting retransmission"),e.retransmitAcceptedResponse(),t=Math.min(2*t,F.T2),this.invite2xxTimer=setTimeout(s,t)):this.invite2xxTimer=void 0};this.invite2xxTimer=setTimeout(s,t);const i=()=>{e.state===ae.Terminated&&(e.removeStateChangeListener(i),this.invite2xxTimer&&(clearTimeout(this.invite2xxTimer),this.invite2xxTimer=void 0),this.reinviteUserAgentServer)};e.addStateChangeListener(i)}}}class Ve extends Ce{constructor(e,t,s){super(Ie,e,t,s),this.confirmedDialogAcks=new Map,this.confirmedDialogs=new Map,this.earlyDialogs=new Map,this.delegate=s}dispose(){this.earlyDialogs.forEach((e=>e.dispose())),this.earlyDialogs.clear(),super.dispose()}onTransportError(e){if(this.transaction.state===ae.Calling)return super.onTransportError(e);this.logger.error(e.message),this.logger.error("User agent client request transport error while sending ACK.")}receiveResponse(e){if(!this.authenticationGuard(e))return;const t=e.statusCode?e.statusCode.toString():"";if(!t)throw new Error("Response status code undefined.");switch(!0){case/^100$/.test(t):return void(this.delegate&&this.delegate.onTrying&&this.delegate.onTrying({message:e}));case/^1[0-9]{2}$/.test(t):{if(!e.toTag)return void this.logger.warn("Non-100 1xx INVITE response received without a to tag, dropping.");if(!e.parseHeader("contact"))return void this.logger.error("Non-100 1xx INVITE response received without a Contact header field, dropping.");const t=$e.initialDialogStateForUserAgentClient(this.message,e);let s=this.earlyDialogs.get(t.id);if(!s){const e=this.transaction;if(!(e instanceof Ie))throw new Error("Transaction not instance of InviteClientTransaction.");s=new Ge(e,this.core,t),this.earlyDialogs.set(s.id,s)}if(!s.reliableSequenceGuard(e))return void this.logger.warn("1xx INVITE reliable response received out of order or is a retransmission, dropping.");s.signalingState!==M.Initial&&s.signalingState!==M.HaveLocalOffer||s.signalingStateTransition(e);const i=s;this.delegate&&this.delegate.onProgress&&this.delegate.onProgress({message:e,session:i,prack:e=>i.prack(void 0,e)})}return;case/^2[0-9]{2}$/.test(t):{if(!e.toTag)return void this.logger.error("2xx INVITE response received without a to tag, dropping.");if(!e.parseHeader("contact"))return void this.logger.error("2xx INVITE response received without a Contact header field, dropping.");const t=$e.initialDialogStateForUserAgentClient(this.message,e);let s=this.confirmedDialogs.get(t.id);if(s){const e=this.confirmedDialogAcks.get(t.id);if(e){const t=this.transaction;if(!(t instanceof Ie))throw new Error("Client transaction not instance of InviteClientTransaction.");t.ackResponse(e.message)}return}if(s=this.earlyDialogs.get(t.id),s)s.confirm(),s.recomputeRouteSet(e),this.earlyDialogs.delete(s.id),this.confirmedDialogs.set(s.id,s);else{const e=this.transaction;if(!(e instanceof Ie))throw new Error("Transaction not instance of InviteClientTransaction.");s=new Ge(e,this.core,t),this.confirmedDialogs.set(s.id,s)}s.signalingState!==M.Initial&&s.signalingState!==M.HaveLocalOffer||s.signalingStateTransition(e);const i=s;if(this.delegate&&this.delegate.onAccept)this.delegate.onAccept({message:e,session:i,ack:e=>{const t=i.ack(e);return this.confirmedDialogAcks.set(i.id,t),t}});else{const e=i.ack();this.confirmedDialogAcks.set(i.id,e)}}return;case/^3[0-9]{2}$/.test(t):return this.earlyDialogs.forEach((e=>e.dispose())),this.earlyDialogs.clear(),void(this.delegate&&this.delegate.onRedirect&&this.delegate.onRedirect({message:e}));case/^[4-6][0-9]{2}$/.test(t):return this.earlyDialogs.forEach((e=>e.dispose())),this.earlyDialogs.clear(),void(this.delegate&&this.delegate.onReject&&this.delegate.onReject({message:e}));default:throw new Error(`Invalid status code ${t}`)}throw new Error(`Executing what should be an unreachable code path receiving ${t} response.`)}}class Ke extends Ce{constructor(e,t,s){super(Se,e,t,s)}}class We extends Ce{constructor(e,t,s){super(Se,e,t,s)}}class Ye extends Ce{constructor(e,t,s){const i=e.createOutgoingRequestMessage(L.SUBSCRIBE,s);super(Se,e.userAgentCore,i,t),this.dialog=e}waitNotifyStop(){}receiveResponse(e){if(e.statusCode&&e.statusCode>=200&&e.statusCode<300){const t=e.getHeader("Expires");if(t){const e=Number(t);this.dialog.subscriptionExpires>e&&(this.dialog.subscriptionExpires=e)}else this.logger.warn("Expires header missing in a 200-class response to SUBSCRIBE")}if(e.statusCode&&e.statusCode>=400&&e.statusCode<700){[404,405,410,416,480,481,482,483,484,485,489,501,604].includes(e.statusCode)&&this.dialog.terminate()}super.receiveResponse(e)}}class Ze extends $e{constructor(e,t,s,i,r,n){super(i,r),this.delegate=n,this._autoRefresh=!1,this._subscriptionEvent=e,this._subscriptionExpires=t,this._subscriptionExpiresInitial=t,this._subscriptionExpiresLastSet=Math.floor(Date.now()/1e3),this._subscriptionRefresh=void 0,this._subscriptionRefreshLastSet=void 0,this._subscriptionState=s,this.logger=i.loggerFactory.getLogger("sip.subscribe-dialog"),this.logger.log(`SUBSCRIBE dialog ${this.id} constructed`)}static initialDialogStateForSubscription(e,t){const s=t.getHeaders("record-route"),i=t.parseHeader("contact");if(!i)throw new Error("Contact undefined.");if(!(i instanceof m))throw new Error("Contact not instance of NameAddrHeader.");const r=i.uri,n=e.cseq,o=e.callId,a=e.fromTag,c=t.fromTag;if(!o)throw new Error("Call id undefined.");if(!a)throw new Error("From tag undefined.");if(!c)throw new Error("To tag undefined.");if(!e.from)throw new Error("From undefined.");if(!e.to)throw new Error("To undefined.");return{id:o+a+c,early:!1,callId:o,localTag:a,remoteTag:c,localSequenceNumber:n,remoteSequenceNumber:undefined,localURI:e.from.uri,remoteURI:e.to.uri,remoteTarget:r,routeSet:s,secure:!1}}dispose(){super.dispose(),this.N&&(clearTimeout(this.N),this.N=void 0),this.refreshTimerClear(),this.logger.log(`SUBSCRIBE dialog ${this.id} destroyed`)}get autoRefresh(){return this._autoRefresh}set autoRefresh(e){this._autoRefresh=!0,this.refreshTimerSet()}get subscriptionEvent(){return this._subscriptionEvent}get subscriptionExpires(){const e=Math.floor(Date.now()/1e3)-this._subscriptionExpiresLastSet,t=this._subscriptionExpires-e;return Math.max(t,0)}set subscriptionExpires(e){if(e<0)throw new Error("Expires must be greater than or equal to zero.");if(this._subscriptionExpires=e,this._subscriptionExpiresLastSet=Math.floor(Date.now()/1e3),this.autoRefresh){const t=this.subscriptionRefresh;(void 0===t||t>=e)&&this.refreshTimerSet()}}get subscriptionExpiresInitial(){return this._subscriptionExpiresInitial}get subscriptionRefresh(){if(void 0===this._subscriptionRefresh||void 0===this._subscriptionRefreshLastSet)return;const e=Math.floor(Date.now()/1e3)-this._subscriptionRefreshLastSet,t=this._subscriptionRefresh-e;return Math.max(t,0)}get subscriptionState(){return this._subscriptionState}receiveRequest(e){if(this.logger.log(`SUBSCRIBE dialog ${this.id} received ${e.method} request`),this.sequenceGuard(e))switch(super.receiveRequest(e),e.method){case L.NOTIFY:this.onNotify(e);break;default:this.logger.log(`SUBSCRIBE dialog ${this.id} received unimplemented ${e.method} request`),this.core.replyStateless(e,{statusCode:501})}else this.logger.log(`SUBSCRIBE dialog ${this.id} rejected out of order ${e.method} request.`)}refresh(){const e="Allow: "+B.toString(),t={};return t.extraHeaders=(t.extraHeaders||[]).slice(),t.extraHeaders.push(e),t.extraHeaders.push("Event: "+this.subscriptionEvent),t.extraHeaders.push("Expires: "+this.subscriptionExpiresInitial),t.extraHeaders.push("Contact: "+this.core.configuration.contact.toString()),this.subscribe(void 0,t)}subscribe(e,t={}){if(this.subscriptionState!==se.Pending&&this.subscriptionState!==se.Active)throw new Error(`Invalid state ${this.subscriptionState}. May only re-subscribe while in state "pending" or "active".`);this.logger.log(`SUBSCRIBE dialog ${this.id} sending SUBSCRIBE request`);const s=new Ye(this,e,t);return this.N&&(clearTimeout(this.N),this.N=void 0),this.N=setTimeout((()=>this.timerN()),F.TIMER_N),s}terminate(){this.stateTransition(se.Terminated),this.onTerminated()}unsubscribe(){const e="Allow: "+B.toString(),t={};return t.extraHeaders=(t.extraHeaders||[]).slice(),t.extraHeaders.push(e),t.extraHeaders.push("Event: "+this.subscriptionEvent),t.extraHeaders.push("Expires: 0"),t.extraHeaders.push("Contact: "+this.core.configuration.contact.toString()),this.subscribe(void 0,t)}onNotify(e){const t=e.parseHeader("Event").event;if(!t||t!==this.subscriptionEvent)return void this.core.replyStateless(e,{statusCode:489});this.N&&(clearTimeout(this.N),this.N=void 0);const s=e.parseHeader("Subscription-State");if(!s||!s.state)return void this.core.replyStateless(e,{statusCode:489});const i=s.state,r=s.expires?Math.max(s.expires,0):void 0;switch(i){case"pending":this.stateTransition(se.Pending,r);break;case"active":this.stateTransition(se.Active,r);break;case"terminated":this.stateTransition(se.Terminated,r);break;default:this.logger.warn("Unrecognized subscription state.")}const n=new Me(this,e);this.delegate&&this.delegate.onNotify?this.delegate.onNotify(n):n.accept()}onRefresh(e){this.delegate&&this.delegate.onRefresh&&this.delegate.onRefresh(e)}onTerminated(){this.delegate&&this.delegate.onTerminated&&this.delegate.onTerminated()}refreshTimerClear(){this.refreshTimer&&(clearTimeout(this.refreshTimer),this.refreshTimer=void 0)}refreshTimerSet(){if(this.refreshTimerClear(),this.autoRefresh&&this.subscriptionExpires>0){const e=900*this.subscriptionExpires;this._subscriptionRefresh=Math.floor(e/1e3),this._subscriptionRefreshLastSet=Math.floor(Date.now()/1e3),this.refreshTimer=setTimeout((()=>{this.refreshTimer=void 0,this._subscriptionRefresh=void 0,this._subscriptionRefreshLastSet=void 0,this.onRefresh(this.refresh())}),e)}}stateTransition(e,t){const s=()=>{this.logger.warn(`Invalid subscription state transition from ${this.subscriptionState} to ${e}`)};switch(e){case se.Initial:case se.NotifyWait:return void s();case se.Pending:if(this.subscriptionState!==se.NotifyWait&&this.subscriptionState!==se.Pending)return void s();break;case se.Active:case se.Terminated:if(this.subscriptionState!==se.NotifyWait&&this.subscriptionState!==se.Pending&&this.subscriptionState!==se.Active)return void s();break;default:return void s()}e===se.Pending&&t&&(this.subscriptionExpires=t),e===se.Active&&t&&(this.subscriptionExpires=t),e===se.Terminated&&this.dispose(),this._subscriptionState=e}timerN(){this.logger.warn("Timer N expired for SUBSCRIBE dialog. Timed out waiting for NOTIFY."),this.subscriptionState!==se.Terminated&&(this.stateTransition(se.Terminated),this.onTerminated())}}class Je extends Ce{constructor(e,t,s){const i=t.getHeader("Event");if(!i)throw new Error("Event undefined");const r=t.getHeader("Expires");if(!r)throw new Error("Expires undefined");super(Se,e,t,s),this.delegate=s,this.subscriberId=t.callId+t.fromTag+i,this.subscriptionExpiresRequested=this.subscriptionExpires=Number(r),this.subscriptionEvent=i,this.subscriptionState=se.NotifyWait,this.waitNotifyStart()}dispose(){super.dispose()}onNotify(e){const t=e.message.parseHeader("Event").event;if(!t||t!==this.subscriptionEvent)return this.logger.warn("Failed to parse event."),void e.reject({statusCode:489});const s=e.message.parseHeader("Subscription-State");if(!s||!s.state)return this.logger.warn("Failed to parse subscription state."),void e.reject({statusCode:489});const i=s.state;switch(i){case"pending":case"active":case"terminated":break;default:return this.logger.warn(`Invalid subscription state ${i}`),void e.reject({statusCode:489})}if("terminated"!==i){if(!e.message.parseHeader("contact"))return this.logger.warn("Failed to parse contact."),void e.reject({statusCode:489})}if(this.dialog)throw new Error("Dialog already created. This implementation only supports install of single subscriptions.");switch(this.waitNotifyStop(),this.subscriptionExpires=s.expires?Math.min(this.subscriptionExpires,Math.max(s.expires,0)):this.subscriptionExpires,i){case"pending":this.subscriptionState=se.Pending;break;case"active":this.subscriptionState=se.Active;break;case"terminated":this.subscriptionState=se.Terminated;break;default:throw new Error(`Unrecognized state ${i}.`)}if(this.subscriptionState!==se.Terminated){const t=Ze.initialDialogStateForSubscription(this.message,e.message);this.dialog=new Ze(this.subscriptionEvent,this.subscriptionExpires,this.subscriptionState,this.core,t)}if(this.delegate&&this.delegate.onNotify){const t=e,s=this.dialog;this.delegate.onNotify({request:t,subscription:s})}else e.accept()}waitNotifyStart(){this.N||(this.core.subscribers.set(this.subscriberId,this),this.N=setTimeout((()=>this.timerN()),F.TIMER_N))}waitNotifyStop(){this.N&&(this.core.subscribers.delete(this.subscriberId),clearTimeout(this.N),this.N=void 0)}receiveResponse(e){if(this.authenticationGuard(e)){if(e.statusCode&&e.statusCode>=200&&e.statusCode<300){const t=e.getHeader("Expires");if(t){const e=Number(t);e>this.subscriptionExpiresRequested&&this.logger.warn("Expires header in a 200-class response to SUBSCRIBE with a higher value than the one in the request"),e<this.subscriptionExpires&&(this.subscriptionExpires=e)}else this.logger.warn("Expires header missing in a 200-class response to SUBSCRIBE");this.dialog&&this.dialog.subscriptionExpires>this.subscriptionExpires&&(this.dialog.subscriptionExpires=this.subscriptionExpires)}e.statusCode&&e.statusCode>=300&&e.statusCode<700&&this.waitNotifyStop(),super.receiveResponse(e)}}timerN(){this.logger.warn("Timer N expired for SUBSCRIBE user agent client. Timed out waiting for NOTIFY."),this.waitNotifyStop(),this.delegate&&this.delegate.onNotifyTimeout&&this.delegate.onNotifyTimeout()}}class ze extends He{constructor(e,t,s){super(Ee,e,t,s),this.core=e}dispose(){this.earlyDialog&&this.earlyDialog.dispose(),super.dispose()}accept(e={statusCode:200}){if(!this.acceptable)throw new O(`${this.message.method} not acceptable in state ${this.transaction.state}.`);if(!this.confirmedDialog)if(this.earlyDialog)this.earlyDialog.confirm(),this.confirmedDialog=this.earlyDialog,this.earlyDialog=void 0;else{const e=this.transaction;if(!(e instanceof Ee))throw new Error("Transaction not instance of InviteClientTransaction.");const t=$e.initialDialogStateForUserAgentServer(this.message,this.toTag);this.confirmedDialog=new Ge(e,this.core,t)}const t=this.message.getHeaders("record-route").map((e=>`Record-Route: ${e}`)),s=`Contact: ${this.core.configuration.contact.toString()}`,i="Allow: "+B.toString();if(!e.body)if(this.confirmedDialog.signalingState===M.Stable)e.body=this.confirmedDialog.answer;else if(this.confirmedDialog.signalingState===M.Initial||this.confirmedDialog.signalingState===M.HaveRemoteOffer)throw new Error("Response must have a body.");e.statusCode=e.statusCode||200,e.extraHeaders=e.extraHeaders||[],e.extraHeaders=e.extraHeaders.concat(t),e.extraHeaders.push(i),e.extraHeaders.push(s);const r=super.accept(e),n=this.confirmedDialog,o=Object.assign(Object.assign({},r),{session:n});return e.body&&this.confirmedDialog.signalingState!==M.Stable&&this.confirmedDialog.signalingStateTransition(e.body),o}progress(e={statusCode:180}){if(!this.progressable)throw new O(`${this.message.method} not progressable in state ${this.transaction.state}.`);if(!this.earlyDialog){const e=this.transaction;if(!(e instanceof Ee))throw new Error("Transaction not instance of InviteClientTransaction.");const t=$e.initialDialogStateForUserAgentServer(this.message,this.toTag,!0);this.earlyDialog=new Ge(e,this.core,t)}const t=this.message.getHeaders("record-route").map((e=>`Record-Route: ${e}`)),s=`Contact: ${this.core.configuration.contact}`;e.extraHeaders=e.extraHeaders||[],e.extraHeaders=e.extraHeaders.concat(t),e.extraHeaders.push(s);const i=super.progress(e),r=this.earlyDialog,n=Object.assign(Object.assign({},i),{session:r});return e.body&&this.earlyDialog.signalingState!==M.Stable&&this.earlyDialog.signalingStateTransition(e.body),n}redirect(e,t={statusCode:302}){return super.redirect(e,t)}reject(e={statusCode:486}){return super.reject(e)}}class Xe extends He{constructor(e,t,s){super(De,e,t,s),this.core=e}}class Qe extends He{constructor(e,t,s){super(De,e,t,s),this.core=e}}const et=["application/sdp","application/dtmf-relay"];class tt{constructor(e,t={}){this.userAgentClients=new Map,this.userAgentServers=new Map,this.configuration=e,this.delegate=t,this.dialogs=new Map,this.subscribers=new Map,this.logger=e.loggerFactory.getLogger("sip.user-agent-core")}dispose(){this.reset()}reset(){this.dialogs.forEach((e=>e.dispose())),this.dialogs.clear(),this.subscribers.forEach((e=>e.dispose())),this.subscribers.clear(),this.userAgentClients.forEach((e=>e.dispose())),this.userAgentClients.clear(),this.userAgentServers.forEach((e=>e.dispose())),this.userAgentServers.clear()}get loggerFactory(){return this.configuration.loggerFactory}get transport(){const e=this.configuration.transportAccessor();if(!e)throw new Error("Transport undefined.");return e}invite(e,t){return new Ve(this,e,t)}message(e,t){return new qe(this,e,t)}publish(e,t){return new Ke(this,e,t)}register(e,t){return new We(this,e,t)}subscribe(e,t){return new Je(this,e,t)}request(e,t){return new Ce(Se,this,e,t)}makeOutgoingRequestMessage(e,t,s,i,r,n,o){const a=this.configuration.sipjsId,c=this.configuration.displayName,h=this.configuration.viaForceRport,d=this.configuration.hackViaTcp,l=this.configuration.supportedOptionTags.slice();e===L.REGISTER&&l.push("path","gruu"),e===L.INVITE&&(this.configuration.contact.pubGruu||this.configuration.contact.tempGruu)&&l.push("gruu");const g={callIdPrefix:a,forceRport:h,fromDisplayName:c,hackViaTcp:d,optionTags:l,routeSet:this.configuration.routeSet,userAgentString:this.configuration.userAgentHeaderFieldValue,viaHost:this.configuration.viaHost},u=Object.assign(Object.assign({},g),r);return new k(e,t,s,i,u,n,o)}receiveIncomingRequestFromTransport(e){this.receiveRequestFromTransport(e)}receiveIncomingResponseFromTransport(e){this.receiveResponseFromTransport(e)}replyStateless(e,t){const s=this.configuration.userAgentHeaderFieldValue,i=this.configuration.supportedOptionTagsResponse;t=Object.assign(Object.assign({},t),{userAgent:s,supported:i});const r=we(e,t);return this.transport.send(r.message).catch((t=>{t instanceof Error&&this.logger.error(t.message),this.logger.error(`Transport error occurred sending stateless reply to ${e.method} request.`)})),r}receiveRequestFromTransport(e){const t=e.viaBranch,s=this.userAgentServers.get(t);e.method===L.ACK&&s&&s.transaction.state===ae.Accepted&&s instanceof ze?this.logger.warn(`Discarding out of dialog ACK after 2xx response sent on transaction ${t}.`):e.method!==L.CANCEL?s?s.transaction.receiveRequest(e):this.receiveRequest(e):s?(this.replyStateless(e,{statusCode:200}),s.transaction instanceof Ee&&s.transaction.state===ae.Proceeding&&s instanceof ze&&s.receiveCancel(e)):this.replyStateless(e,{statusCode:481})}receiveRequest(e){if(!B.includes(e.method)){const t="Allow: "+B.toString();return void this.replyStateless(e,{statusCode:405,extraHeaders:[t]})}if(!e.ruri)throw new Error("Request-URI undefined.");if("sip"!==e.ruri.scheme)return void this.replyStateless(e,{statusCode:416});const t=e.ruri,s=e=>!!e&&e.user===t.user;if(!s(this.configuration.aor)&&!(s(this.configuration.contact.uri)||s(this.configuration.contact.pubGruu)||s(this.configuration.contact.tempGruu)))return this.logger.warn("Request-URI does not point to us."),void(e.method!==L.ACK&&this.replyStateless(e,{statusCode:404}));if(e.method!==L.INVITE||e.hasHeader("Contact")){if(!e.toTag){const t=e.viaBranch;if(!this.userAgentServers.has(t)){if(Array.from(this.userAgentServers.values()).some((t=>t.transaction.request.fromTag===e.fromTag&&t.transaction.request.callId===e.callId&&t.transaction.request.cseq===e.cseq)))return void this.replyStateless(e,{statusCode:482})}}e.toTag?this.receiveInsideDialogRequest(e):this.receiveOutsideDialogRequest(e)}else this.replyStateless(e,{statusCode:400,reasonPhrase:"Missing Contact Header"})}receiveInsideDialogRequest(e){if(e.method===L.NOTIFY){const t=e.parseHeader("Event");if(!t||!t.event)return void this.replyStateless(e,{statusCode:489});const s=e.callId+e.toTag+t.event,i=this.subscribers.get(s);if(i){const t=new Me(this,e);return void i.onNotify(t)}}const t=e.callId+e.toTag+e.fromTag,s=this.dialogs.get(t);if(s){if(e.method===L.OPTIONS){const t="Allow: "+B.toString(),s="Accept: "+et.toString();return void this.replyStateless(e,{statusCode:200,extraHeaders:[t,s]})}s.receiveRequest(e)}else e.method!==L.ACK&&this.replyStateless(e,{statusCode:481})}receiveOutsideDialogRequest(e){switch(e.method){case L.ACK:break;case L.BYE:this.replyStateless(e,{statusCode:481});break;case L.CANCEL:throw new Error(`Unexpected out of dialog request method ${e.method}.`);case L.INFO:this.replyStateless(e,{statusCode:405});break;case L.INVITE:{const t=new ze(this,e);this.delegate.onInvite?this.delegate.onInvite(t):t.reject()}break;case L.MESSAGE:{const t=new xe(this,e);this.delegate.onMessage?this.delegate.onMessage(t):t.accept()}break;case L.NOTIFY:{const t=new Me(this,e);this.delegate.onNotify?this.delegate.onNotify(t):t.reject({statusCode:405})}break;case L.OPTIONS:{const t="Allow: "+B.toString(),s="Accept: "+et.toString();this.replyStateless(e,{statusCode:200,extraHeaders:[t,s]})}break;case L.REFER:{const t=new Be(this,e);this.delegate.onRefer?this.delegate.onRefer(t):t.reject({statusCode:405})}break;case L.REGISTER:{const t=new Xe(this,e);this.delegate.onRegister?this.delegate.onRegister(t):t.reject({statusCode:405})}break;case L.SUBSCRIBE:{const t=new Qe(this,e);this.delegate.onSubscribe?this.delegate.onSubscribe(t):t.reject({statusCode:480})}break;default:throw new Error(`Unexpected out of dialog request method ${e.method}.`)}}receiveResponseFromTransport(e){if(e.getHeaders("via").length>1)return void this.logger.warn("More than one Via header field present in the response, dropping");const t=e.viaBranch+e.method,s=this.userAgentClients.get(t);s?s.transaction.receiveResponse(e):this.logger.warn(`Discarding unmatched ${e.statusCode} response to ${e.method} ${t}.`)}}var st;function it(){return e=>e.audio||e.video?void 0===navigator.mediaDevices?Promise.reject(new Error("Media devices not available in insecure contexts.")):navigator.mediaDevices.getUserMedia.call(navigator.mediaDevices,e):Promise.resolve(new MediaStream)}function rt(){return{bundlePolicy:"balanced",certificates:void 0,iceCandidatePoolSize:0,iceServers:[{urls:"stun:stun.l.google.com:19302"}],iceTransportPolicy:"all",peerIdentity:void 0,rtcpMuxPolicy:"require"}}!function(e){function t(e,t){let s=t,i=0,r=0;if(e.substring(s,s+2).match(/(^\r\n)/))return-2;for(;0===i;){if(r=e.indexOf("\r\n",s),-1===r)return r;!e.substring(r+2,r+4).match(/(^\r\n)/)&&e.charAt(r+2).match(/(^\s+)/)?s=r+2:i=r}return i}function s(e,t,s,i){const r=t.indexOf(":",s),n=t.substring(s,r).trim(),o=t.substring(r+1,i).trim();let a;switch(n.toLowerCase()){case"via":case"v":e.addHeader("via",o),1===e.getHeaders("via").length?(a=e.parseHeader("Via"),a&&(e.via=a,e.viaBranch=a.branch)):a=0;break;case"from":case"f":e.setHeader("from",o),a=e.parseHeader("from"),a&&(e.from=a,e.fromTag=a.getParam("tag"));break;case"to":case"t":e.setHeader("to",o),a=e.parseHeader("to"),a&&(e.to=a,e.toTag=a.getParam("tag"));break;case"record-route":if(a=y.parse(o,"Record_Route"),-1===a){a=void 0;break}if(!(a instanceof Array)){a=void 0;break}a.forEach((t=>{e.addHeader("record-route",o.substring(t.position,t.offset)),e.headers["Record-Route"][e.getHeaders("record-route").length-1].parsed=t.parsed}));break;case"call-id":case"i":e.setHeader("call-id",o),a=e.parseHeader("call-id"),a&&(e.callId=o);break;case"contact":case"m":if(a=y.parse(o,"Contact"),-1===a){a=void 0;break}if(!(a instanceof Array)){a=void 0;break}a.forEach((t=>{e.addHeader("contact",o.substring(t.position,t.offset)),e.headers.Contact[e.getHeaders("contact").length-1].parsed=t.parsed}));break;case"content-length":case"l":e.setHeader("content-length",o),a=e.parseHeader("content-length");break;case"content-type":case"c":e.setHeader("content-type",o),a=e.parseHeader("content-type");break;case"cseq":e.setHeader("cseq",o),a=e.parseHeader("cseq"),a&&(e.cseq=a.value),e instanceof H&&(e.method=a.method);break;case"max-forwards":e.setHeader("max-forwards",o),a=e.parseHeader("max-forwards");break;case"www-authenticate":e.setHeader("www-authenticate",o),a=e.parseHeader("www-authenticate");break;case"proxy-authenticate":e.setHeader("proxy-authenticate",o),a=e.parseHeader("proxy-authenticate");break;case"refer-to":case"r":e.setHeader("refer-to",o),a=e.parseHeader("refer-to"),a&&(e.referTo=a);break;default:e.addHeader(n.toLowerCase(),o),a=0}return void 0!==a||{error:"error parsing header '"+n+"'"}}e.getHeader=t,e.parseHeader=s,e.parseMessage=function(e,i){let r=0,n=e.indexOf("\r\n");if(-1===n)return void i.warn("no CRLF found, not a SIP message, discarded");const o=e.substring(0,n),a=y.parse(o,"Request_Response");let c,h;if(-1!==a){for(a.status_code?(c=new H,c.statusCode=a.status_code,c.reasonPhrase=a.reason_phrase):(c=new D,c.method=a.method,c.ruri=a.uri),c.data=e,r=n+2;;){if(n=t(e,r),-2===n){h=r+2;break}if(-1===n)return void i.error("malformed message");const o=s(c,e,r,n);if(o&&!0!==o)return void i.error(o.error);r=n+2}return c.hasHeader("content-length")?c.body=e.substr(h,Number(c.getHeader("content-length"))):c.body=e.substring(h),c}i.warn('error parsing first line of SIP message: "'+o+'"')}}(st||(st={}));class nt{constructor(e,t,s){e.debug("SessionDescriptionHandler.constructor"),this.logger=e,this.mediaStreamFactory=t,this.sessionDescriptionHandlerConfiguration=s,this._localMediaStream=new MediaStream,this._remoteMediaStream=new MediaStream,this._peerConnection=new RTCPeerConnection(null==s?void 0:s.peerConnectionConfiguration),this.initPeerConnectionEventHandlers()}get localMediaStream(){return this._localMediaStream}get remoteMediaStream(){return this._remoteMediaStream}get dataChannel(){return this._dataChannel}get peerConnection(){return this._peerConnection}get peerConnectionDelegate(){return this._peerConnectionDelegate}set peerConnectionDelegate(e){this._peerConnectionDelegate=e}static dispatchAddTrackEvent(e,t){e.dispatchEvent(new MediaStreamTrackEvent("addtrack",{track:t}))}static dispatchRemoveTrackEvent(e,t){e.dispatchEvent(new MediaStreamTrackEvent("removetrack",{track:t}))}close(){this.logger.debug("SessionDescriptionHandler.close"),void 0!==this._peerConnection&&(this._peerConnection.getReceivers().forEach((e=>{e.track&&e.track.stop()})),this._peerConnection.getSenders().forEach((e=>{e.track&&e.track.stop()})),this._dataChannel&&this._dataChannel.close(),this._peerConnection.close(),this._peerConnection=void 0)}getDescription(e,t){var s,i;if(this.logger.debug("SessionDescriptionHandler.getDescription"),void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));this.onDataChannel=null==e?void 0:e.onDataChannel;const r=null===(s=null==e?void 0:e.offerOptions)||void 0===s?void 0:s.iceRestart,n=void 0===(null==e?void 0:e.iceGatheringTimeout)?null===(i=this.sessionDescriptionHandlerConfiguration)||void 0===i?void 0:i.iceGatheringTimeout:null==e?void 0:e.iceGatheringTimeout;return this.getLocalMediaStream(e).then((()=>this.updateDirection(e))).then((()=>this.createDataChannel(e))).then((()=>this.createLocalOfferOrAnswer(e))).then((e=>this.applyModifiers(e,t))).then((e=>this.setLocalSessionDescription(e))).then((()=>this.waitForIceGatheringComplete(r,n))).then((()=>this.getLocalSessionDescription())).then((e=>({body:e.sdp,contentType:"application/sdp"}))).catch((e=>{throw this.logger.error("SessionDescriptionHandler.getDescription failed - "+e),e}))}hasDescription(e){return this.logger.debug("SessionDescriptionHandler.hasDescription"),"application/sdp"===e}sendDtmf(e,t){if(this.logger.debug("SessionDescriptionHandler.sendDtmf"),void 0===this._peerConnection)return this.logger.error("SessionDescriptionHandler.sendDtmf failed - peer connection closed"),!1;const s=this._peerConnection.getSenders();if(0===s.length)return this.logger.error("SessionDescriptionHandler.sendDtmf failed - no senders"),!1;const i=s[0].dtmf;if(!i)return this.logger.error("SessionDescriptionHandler.sendDtmf failed - no DTMF sender"),!1;const r=null==t?void 0:t.duration,n=null==t?void 0:t.interToneGap;try{i.insertDTMF(e,r,n)}catch(e){return this.logger.error(e),!1}return this.logger.log("SessionDescriptionHandler.sendDtmf sent via RTP: "+e.toString()),!0}setDescription(e,t,s){if(this.logger.debug("SessionDescriptionHandler.setDescription"),void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));this.onDataChannel=null==t?void 0:t.onDataChannel;const i="have-local-offer"===this._peerConnection.signalingState?"answer":"offer";return this.getLocalMediaStream(t).then((()=>this.applyModifiers({sdp:e,type:i},s))).then((e=>this.setRemoteSessionDescription(e))).catch((e=>{throw this.logger.error("SessionDescriptionHandler.setDescription failed - "+e),e}))}applyModifiers(e,t){return this.logger.debug("SessionDescriptionHandler.applyModifiers"),t&&0!==t.length?t.reduce(((e,t)=>e.then(t)),Promise.resolve(e)).then((e=>{if(this.logger.debug("SessionDescriptionHandler.applyModifiers - modified sdp"),!e.sdp||!e.type)throw new Error("Invalid SDP.");return{sdp:e.sdp,type:e.type}})):Promise.resolve(e)}createDataChannel(e){if(void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));if(!0!==(null==e?void 0:e.dataChannel))return Promise.resolve();if(this._dataChannel)return Promise.resolve();switch(this._peerConnection.signalingState){case"stable":this.logger.debug("SessionDescriptionHandler.createDataChannel - creating data channel");try{return this._dataChannel=this._peerConnection.createDataChannel((null==e?void 0:e.dataChannelLabel)||"",null==e?void 0:e.dataChannelOptions),this.onDataChannel&&this.onDataChannel(this._dataChannel),Promise.resolve()}catch(e){return Promise.reject(e)}case"have-remote-offer":return Promise.resolve();case"have-local-offer":case"have-local-pranswer":case"have-remote-pranswer":case"closed":default:return Promise.reject(new Error("Invalid signaling state "+this._peerConnection.signalingState))}}createLocalOfferOrAnswer(e){if(void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));switch(this._peerConnection.signalingState){case"stable":return this.logger.debug("SessionDescriptionHandler.createLocalOfferOrAnswer - creating SDP offer"),this._peerConnection.createOffer(null==e?void 0:e.offerOptions);case"have-remote-offer":return this.logger.debug("SessionDescriptionHandler.createLocalOfferOrAnswer - creating SDP answer"),this._peerConnection.createAnswer(null==e?void 0:e.answerOptions);case"have-local-offer":case"have-local-pranswer":case"have-remote-pranswer":case"closed":default:return Promise.reject(new Error("Invalid signaling state "+this._peerConnection.signalingState))}}getLocalMediaStream(e){if(this.logger.debug("SessionDescriptionHandler.getLocalMediaStream"),void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));let t=Object.assign({},null==e?void 0:e.constraints);if(this.localMediaStreamConstraints){if(t.audio=t.audio||this.localMediaStreamConstraints.audio,t.video=t.video||this.localMediaStreamConstraints.video,JSON.stringify(this.localMediaStreamConstraints.audio)===JSON.stringify(t.audio)&&JSON.stringify(this.localMediaStreamConstraints.video)===JSON.stringify(t.video))return Promise.resolve()}else void 0===t.audio&&void 0===t.video&&(t={audio:!0});return this.localMediaStreamConstraints=t,this.mediaStreamFactory(t,this).then((e=>this.setLocalMediaStream(e)))}setLocalMediaStream(e){if(this.logger.debug("SessionDescriptionHandler.setLocalMediaStream"),!this._peerConnection)throw new Error("Peer connection undefined.");const t=this._peerConnection,s=this._localMediaStream,i=[],r=e=>{const r=e.kind;if("audio"!==r&&"video"!==r)throw new Error(`Unknown new track kind ${r}.`);const n=t.getSenders().find((e=>e.track&&e.track.kind===r));n?i.push(new Promise((e=>{this.logger.debug(`SessionDescriptionHandler.setLocalMediaStream - replacing sender ${r} track`),e()})).then((()=>n.replaceTrack(e).then((()=>{const t=s.getTracks().find((e=>e.kind===r));t&&(t.stop(),s.removeTrack(t),nt.dispatchRemoveTrackEvent(s,t)),s.addTrack(e),nt.dispatchAddTrackEvent(s,e)})).catch((e=>{throw this.logger.error(`SessionDescriptionHandler.setLocalMediaStream - failed to replace sender ${r} track`),e}))))):i.push(new Promise((e=>{this.logger.debug(`SessionDescriptionHandler.setLocalMediaStream - adding sender ${r} track`),e()})).then((()=>{try{t.addTrack(e,s)}catch(e){throw this.logger.error(`SessionDescriptionHandler.setLocalMediaStream - failed to add sender ${r} track`),e}s.addTrack(e),nt.dispatchAddTrackEvent(s,e)})))},n=e.getAudioTracks();n.length&&r(n[0]);const o=e.getVideoTracks();return o.length&&r(o[0]),i.reduce(((e,t)=>e.then((()=>t))),Promise.resolve())}getLocalSessionDescription(){if(this.logger.debug("SessionDescriptionHandler.getLocalSessionDescription"),void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));const e=this._peerConnection.localDescription;return e?Promise.resolve(e):Promise.reject(new Error("Failed to get local session description"))}setLocalSessionDescription(e){return this.logger.debug("SessionDescriptionHandler.setLocalSessionDescription"),void 0===this._peerConnection?Promise.reject(new Error("Peer connection closed.")):this._peerConnection.setLocalDescription(e)}setRemoteSessionDescription(e){if(this.logger.debug("SessionDescriptionHandler.setRemoteSessionDescription"),void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));const t=e.sdp;let s;switch(this._peerConnection.signalingState){case"stable":s="offer";break;case"have-local-offer":s="answer";break;case"have-local-pranswer":case"have-remote-offer":case"have-remote-pranswer":case"closed":default:return Promise.reject(new Error("Invalid signaling state "+this._peerConnection.signalingState))}return t?this._peerConnection.setRemoteDescription({sdp:t,type:s}):(this.logger.error("SessionDescriptionHandler.setRemoteSessionDescription failed - cannot set null sdp"),Promise.reject(new Error("SDP is undefined")))}setRemoteTrack(e){this.logger.debug("SessionDescriptionHandler.setRemoteTrack");const t=this._remoteMediaStream;t.getTrackById(e.id)?this.logger.debug(`SessionDescriptionHandler.setRemoteTrack - have remote ${e.kind} track`):"audio"===e.kind?(this.logger.debug(`SessionDescriptionHandler.setRemoteTrack - adding remote ${e.kind} track`),t.getAudioTracks().forEach((e=>{e.stop(),t.removeTrack(e),nt.dispatchRemoveTrackEvent(t,e)})),t.addTrack(e),nt.dispatchAddTrackEvent(t,e)):"video"===e.kind&&(this.logger.debug(`SessionDescriptionHandler.setRemoteTrack - adding remote ${e.kind} track`),t.getVideoTracks().forEach((e=>{e.stop(),t.removeTrack(e),nt.dispatchRemoveTrackEvent(t,e)})),t.addTrack(e),nt.dispatchAddTrackEvent(t,e))}updateDirection(e){if(void 0===this._peerConnection)return Promise.reject(new Error("Peer connection closed."));switch(this._peerConnection.signalingState){case"stable":this.logger.debug("SessionDescriptionHandler.updateDirection - setting offer direction");{const t=t=>{switch(t){case"inactive":case"recvonly":return(null==e?void 0:e.hold)?"inactive":"recvonly";case"sendonly":case"sendrecv":return(null==e?void 0:e.hold)?"sendonly":"sendrecv";case"stopped":return"stopped";default:throw new Error("Should never happen")}};this._peerConnection.getTransceivers().forEach((e=>{if(e.direction){const s=t(e.direction);e.direction!==s&&(e.direction=s)}}))}break;case"have-remote-offer":this.logger.debug("SessionDescriptionHandler.updateDirection - setting answer direction");{const t=(()=>{const e=this._peerConnection.remoteDescription;if(!e)throw new Error("Failed to read remote offer");const t=/a=sendrecv\r\n|a=sendonly\r\n|a=recvonly\r\n|a=inactive\r\n/.exec(e.sdp);if(t)switch(t[0]){case"a=inactive\r\n":return"inactive";case"a=recvonly\r\n":return"recvonly";case"a=sendonly\r\n":return"sendonly";case"a=sendrecv\r\n":return"sendrecv";default:throw new Error("Should never happen")}return"sendrecv"})(),s=(()=>{switch(t){case"inactive":return"inactive";case"recvonly":return"sendonly";case"sendonly":return(null==e?void 0:e.hold)?"inactive":"recvonly";case"sendrecv":return(null==e?void 0:e.hold)?"sendonly":"sendrecv";default:throw new Error("Should never happen")}})();this._peerConnection.getTransceivers().forEach((e=>{e.direction&&"stopped"!==e.direction&&e.direction!==s&&(e.direction=s)}))}break;case"have-local-offer":case"have-local-pranswer":case"have-remote-pranswer":case"closed":default:return Promise.reject(new Error("Invalid signaling state "+this._peerConnection.signalingState))}return Promise.resolve()}iceGatheringComplete(){this.logger.debug("SessionDescriptionHandler.iceGatheringComplete"),void 0!==this.iceGatheringCompleteTimeoutId&&(this.logger.debug("SessionDescriptionHandler.iceGatheringComplete - clearing timeout"),clearTimeout(this.iceGatheringCompleteTimeoutId),this.iceGatheringCompleteTimeoutId=void 0),void 0!==this.iceGatheringCompletePromise&&(this.logger.debug("SessionDescriptionHandler.iceGatheringComplete - resolving promise"),this.iceGatheringCompleteResolve&&this.iceGatheringCompleteResolve(),this.iceGatheringCompletePromise=void 0,this.iceGatheringCompleteResolve=void 0,this.iceGatheringCompleteReject=void 0)}waitForIceGatheringComplete(e=!1,t=0){return this.logger.debug("SessionDescriptionHandler.waitForIceGatheringToComplete"),void 0===this._peerConnection?Promise.reject("Peer connection closed."):e||"complete"!==this._peerConnection.iceGatheringState?(void 0!==this.iceGatheringCompletePromise&&(this.logger.debug("SessionDescriptionHandler.waitForIceGatheringToComplete - rejecting prior waiting promise"),this.iceGatheringCompleteReject&&this.iceGatheringCompleteReject(new Error("Promise superseded.")),this.iceGatheringCompletePromise=void 0,this.iceGatheringCompleteResolve=void 0,this.iceGatheringCompleteReject=void 0),this.iceGatheringCompletePromise=new Promise(((e,s)=>{this.iceGatheringCompleteResolve=e,this.iceGatheringCompleteReject=s,t>0&&(this.logger.debug("SessionDescriptionHandler.waitForIceGatheringToComplete - timeout in "+t),this.iceGatheringCompleteTimeoutId=setTimeout((()=>{this.logger.debug("SessionDescriptionHandler.waitForIceGatheringToComplete - timeout"),this.iceGatheringComplete()}),t))})),this.iceGatheringCompletePromise):(this.logger.debug("SessionDescriptionHandler.waitForIceGatheringToComplete - already complete"),Promise.resolve())}initPeerConnectionEventHandlers(){if(this.logger.debug("SessionDescriptionHandler.initPeerConnectionEventHandlers"),!this._peerConnection)throw new Error("Peer connection undefined.");const e=this._peerConnection;e.onconnectionstatechange=t=>{var s;const i=e.connectionState;this.logger.debug(`SessionDescriptionHandler.onconnectionstatechange ${i}`),(null===(s=this._peerConnectionDelegate)||void 0===s?void 0:s.onconnectionstatechange)&&this._peerConnectionDelegate.onconnectionstatechange(t)},e.ondatachannel=e=>{var t;this.logger.debug("SessionDescriptionHandler.ondatachannel"),this._dataChannel=e.channel,this.onDataChannel&&this.onDataChannel(this._dataChannel),(null===(t=this._peerConnectionDelegate)||void 0===t?void 0:t.ondatachannel)&&this._peerConnectionDelegate.ondatachannel(e)},e.onicecandidate=e=>{var t;this.logger.debug("SessionDescriptionHandler.onicecandidate"),(null===(t=this._peerConnectionDelegate)||void 0===t?void 0:t.onicecandidate)&&this._peerConnectionDelegate.onicecandidate(e)},e.onicecandidateerror=e=>{var t;this.logger.debug("SessionDescriptionHandler.onicecandidateerror"),(null===(t=this._peerConnectionDelegate)||void 0===t?void 0:t.onicecandidateerror)&&this._peerConnectionDelegate.onicecandidateerror(e)},e.oniceconnectionstatechange=t=>{var s;const i=e.iceConnectionState;this.logger.debug(`SessionDescriptionHandler.oniceconnectionstatechange ${i}`),(null===(s=this._peerConnectionDelegate)||void 0===s?void 0:s.oniceconnectionstatechange)&&this._peerConnectionDelegate.oniceconnectionstatechange(t)},e.onicegatheringstatechange=t=>{var s;const i=e.iceGatheringState;this.logger.debug(`SessionDescriptionHandler.onicegatheringstatechange ${i}`),"complete"===i&&this.iceGatheringComplete(),(null===(s=this._peerConnectionDelegate)||void 0===s?void 0:s.onicegatheringstatechange)&&this._peerConnectionDelegate.onicegatheringstatechange(t)},e.onnegotiationneeded=e=>{var t;this.logger.debug("SessionDescriptionHandler.onnegotiationneeded"),(null===(t=this._peerConnectionDelegate)||void 0===t?void 0:t.onnegotiationneeded)&&this._peerConnectionDelegate.onnegotiationneeded(e)},e.onsignalingstatechange=t=>{var s;const i=e.signalingState;this.logger.debug(`SessionDescriptionHandler.onsignalingstatechange ${i}`),(null===(s=this._peerConnectionDelegate)||void 0===s?void 0:s.onsignalingstatechange)&&this._peerConnectionDelegate.onsignalingstatechange(t)},e.onstatsended=e=>{var t;this.logger.debug("SessionDescriptionHandler.onstatsended"),(null===(t=this._peerConnectionDelegate)||void 0===t?void 0:t.onstatsended)&&this._peerConnectionDelegate.onstatsended(e)},e.ontrack=e=>{var t;const s=e.track.kind,i=e.track.enabled?"enabled":"disabled";this.logger.debug(`SessionDescriptionHandler.ontrack ${s} ${i}`),this.setRemoteTrack(e.track),(null===(t=this._peerConnectionDelegate)||void 0===t?void 0:t.ontrack)&&this._peerConnectionDelegate.ontrack(e)}}}function ot(e){return(t,s)=>{void 0===e&&(e=it());const i={iceGatheringTimeout:void 0!==(null==s?void 0:s.iceGatheringTimeout)?null==s?void 0:s.iceGatheringTimeout:5e3,peerConnectionConfiguration:Object.assign(Object.assign({},{bundlePolicy:"balanced",certificates:void 0,iceCandidatePoolSize:0,iceServers:[{urls:"stun:stun.l.google.com:19302"}],iceTransportPolicy:"all",peerIdentity:void 0,rtcpMuxPolicy:"require"}),null==s?void 0:s.peerConnectionConfiguration)},r=t.userAgent.getLogger("sip.SessionDescriptionHandler");return new nt(r,e,i)}}class at{constructor(e,t){if(this._state=re.Disconnected,this.transitioningState=!1,this._stateEventEmitter=new u,this.logger=e,t){const e=t,s=null==e?void 0:e.wsServers,i=null==e?void 0:e.maxReconnectionAttempts;if(void 0!==s){const e='The transport option "wsServers" as has apparently been specified and has been deprecated. It will no longer be available starting with SIP.js release 0.16.0. Please update accordingly.';this.logger.warn(e)}if(void 0!==i){const e='The transport option "maxReconnectionAttempts" as has apparently been specified and has been deprecated. It will no longer be available starting with SIP.js release 0.16.0. Please update accordingly.';this.logger.warn(e)}s&&!t.server&&("string"==typeof s&&(t.server=s),s instanceof Array&&(t.server=s[0]))}this.configuration=Object.assign(Object.assign({},at.defaultOptions),t);const s=this.configuration.server,i=y.parse(s,"absoluteURI");if(-1===i)throw this.logger.error(`Invalid WebSocket Server URL "${s}"`),new Error("Invalid WebSocket Server URL");if(!["wss","ws","udp"].includes(i.scheme))throw this.logger.error(`Invalid scheme in WebSocket Server URL "${s}"`),new Error("Invalid scheme in WebSocket Server URL");this._protocol=i.scheme.toUpperCase()}dispose(){return this.disconnect()}get protocol(){return this._protocol}get server(){return this.configuration.server}get state(){return this._state}get stateChange(){return this._stateEventEmitter}get ws(){return this._ws}connect(){return this._connect()}disconnect(){return this._disconnect()}isConnected(){return this.state===re.Connected}send(e){return this._send(e)}_connect(){switch(this.logger.log(`Connecting ${this.server}`),this.state){case re.Connecting:if(this.transitioningState)return Promise.reject(this.transitionLoopDetectedError(re.Connecting));if(!this.connectPromise)throw new Error("Connect promise must be defined.");return this.connectPromise;case re.Connected:if(this.transitioningState)return Promise.reject(this.transitionLoopDetectedError(re.Connecting));if(this.connectPromise)throw new Error("Connect promise must not be defined.");return Promise.resolve();case re.Disconnecting:if(this.connectPromise)throw new Error("Connect promise must not be defined.");try{this.transitionState(re.Connecting)}catch(e){if(e instanceof d)return Promise.reject(e);throw e}break;case re.Disconnected:if(this.connectPromise)throw new Error("Connect promise must not be defined.");try{this.transitionState(re.Connecting)}catch(e){if(e instanceof d)return Promise.reject(e);throw e}break;default:throw new Error("Unknown state")}let e;try{e=new WebSocket(this.server,"sip"),e.binaryType="arraybuffer",e.addEventListener("close",(t=>this.onWebSocketClose(t,e))),e.addEventListener("error",(t=>this.onWebSocketError(t,e))),e.addEventListener("open",(t=>this.onWebSocketOpen(t,e))),e.addEventListener("message",(t=>this.onWebSocketMessage(t,e))),this._ws=e}catch(e){return this._ws=void 0,this.logger.error("WebSocket construction failed."),this.logger.error(e),new Promise(((t,s)=>{this.connectResolve=t,this.connectReject=s,this.transitionState(re.Disconnected,e)}))}return this.connectPromise=new Promise(((t,s)=>{this.connectResolve=t,this.connectReject=s,this.connectTimeout=setTimeout((()=>{this.logger.warn("Connect timed out. Exceeded time set in configuration.connectionTimeout: "+this.configuration.connectionTimeout+"s."),e.close(1e3)}),1e3*this.configuration.connectionTimeout)})),this.connectPromise}_disconnect(){switch(this.logger.log(`Disconnecting ${this.server}`),this.state){case re.Connecting:if(this.disconnectPromise)throw new Error("Disconnect promise must not be defined.");try{this.transitionState(re.Disconnecting)}catch(e){if(e instanceof d)return Promise.reject(e);throw e}break;case re.Connected:if(this.disconnectPromise)throw new Error("Disconnect promise must not be defined.");try{this.transitionState(re.Disconnecting)}catch(e){if(e instanceof d)return Promise.reject(e);throw e}break;case re.Disconnecting:if(this.transitioningState)return Promise.reject(this.transitionLoopDetectedError(re.Disconnecting));if(!this.disconnectPromise)throw new Error("Disconnect promise must be defined.");return this.disconnectPromise;case re.Disconnected:if(this.transitioningState)return Promise.reject(this.transitionLoopDetectedError(re.Disconnecting));if(this.disconnectPromise)throw new Error("Disconnect promise must not be defined.");return Promise.resolve();default:throw new Error("Unknown state")}if(!this._ws)throw new Error("WebSocket must be defined.");const e=this._ws;return this.disconnectPromise=new Promise(((t,s)=>{this.disconnectResolve=t,this.disconnectReject=s;try{e.close(1e3)}catch(e){throw this.logger.error("WebSocket close failed."),this.logger.error(e),e}})),this.disconnectPromise}_send(e){if(!0===this.configuration.traceSip&&this.logger.log("Sending WebSocket message:\n\n"+e+"\n"),this._state!==re.Connected)return Promise.reject(new Error("Not connected."));if(!this._ws)throw new Error("WebSocket undefined.");try{this._ws.send(e)}catch(e){return e instanceof Error?Promise.reject(e):Promise.reject(new Error("WebSocket send failed."))}return Promise.resolve()}onWebSocketClose(e,t){if(t!==this._ws)return;const s=`WebSocket closed ${this.server} (code: ${e.code})`,i=this.disconnectPromise?void 0:new Error(s);i&&this.logger.warn("WebSocket closed unexpectedly"),this.logger.log(s),this._ws=void 0,this.transitionState(re.Disconnected,i)}onWebSocketError(e,t){t===this._ws&&this.logger.error("WebSocket error occurred.")}onWebSocketMessage(e,t){if(t!==this._ws)return;const s=e.data;let i;if(/^(\r\n)+$/.test(s))return this.clearKeepAliveTimeout(),void(!0===this.configuration.traceSip&&this.logger.log("Received WebSocket message with CRLF Keep Alive response"));if(s){if("string"!=typeof s){try{i=(new TextDecoder).decode(new Uint8Array(s))}catch(e){return this.logger.error(e),void this.logger.error("Received WebSocket binary message failed to be converted into string, message discarded")}!0===this.configuration.traceSip&&this.logger.log("Received WebSocket binary message:\n\n"+i+"\n")}else i=s,!0===this.configuration.traceSip&&this.logger.log("Received WebSocket text message:\n\n"+i+"\n");if(this.state===re.Connected){if(this.onMessage)try{this.onMessage(i)}catch(e){throw this.logger.error(e),this.logger.error("Exception thrown by onMessage callback"),e}}else this.logger.warn("Received message while not connected, discarding...")}else this.logger.warn("Received empty message, discarding...")}onWebSocketOpen(e,t){t===this._ws&&this._state===re.Connecting&&(this.logger.log(`WebSocket opened ${this.server}`),this.transitionState(re.Connected))}transitionLoopDetectedError(e){let t="A state transition loop has been detected.";return t+=` An attempt to transition from ${this._state} to ${e} before the prior transition completed.`,t+=" Perhaps you are synchronously calling connect() or disconnect() from a callback or state change handler?",this.logger.error(t),new d("Loop detected.")}transitionState(e,t){const s=()=>{throw new Error(`Invalid state transition from ${this._state} to ${e}`)};if(this.transitioningState)throw this.transitionLoopDetectedError(e);switch(this.transitioningState=!0,this._state){case re.Connecting:e!==re.Connected&&e!==re.Disconnecting&&e!==re.Disconnected&&s();break;case re.Connected:e!==re.Disconnecting&&e!==re.Disconnected&&s();break;case re.Disconnecting:e!==re.Connecting&&e!==re.Disconnected&&s();break;case re.Disconnected:e!==re.Connecting&&s();break;default:throw new Error("Unknown state.")}const i=this._state;this._state=e;const r=this.connectResolve,n=this.connectReject;i===re.Connecting&&(this.connectPromise=void 0,this.connectResolve=void 0,this.connectReject=void 0);const o=this.disconnectResolve,a=this.disconnectReject;if(i===re.Disconnecting&&(this.disconnectPromise=void 0,this.disconnectResolve=void 0,this.disconnectReject=void 0),this.connectTimeout&&(clearTimeout(this.connectTimeout),this.connectTimeout=void 0),this.logger.log(`Transitioned from ${i} to ${this._state}`),this._stateEventEmitter.emit(this._state),e===re.Connected&&(this.startSendingKeepAlives(),this.onConnect))try{this.onConnect()}catch(e){throw this.logger.error(e),this.logger.error("Exception thrown by onConnect callback"),e}if(i===re.Connected&&(this.stopSendingKeepAlives(),this.onDisconnect))try{t?this.onDisconnect(t):this.onDisconnect()}catch(e){throw this.logger.error(e),this.logger.error("Exception thrown by onDisconnect callback"),e}if(i===re.Connecting){if(!r)throw new Error("Connect resolve undefined.");if(!n)throw new Error("Connect reject undefined.");e===re.Connected?r():n(t||new Error("Connect aborted."))}if(i===re.Disconnecting){if(!o)throw new Error("Disconnect resolve undefined.");if(!a)throw new Error("Disconnect reject undefined.");e===re.Disconnected?o():a(t||new Error("Disconnect aborted."))}this.transitioningState=!1}clearKeepAliveTimeout(){this.keepAliveDebounceTimeout&&clearTimeout(this.keepAliveDebounceTimeout),this.keepAliveDebounceTimeout=void 0}sendKeepAlive(){return this.keepAliveDebounceTimeout?Promise.resolve():(this.keepAliveDebounceTimeout=setTimeout((()=>{this.clearKeepAliveTimeout()}),1e3*this.configuration.keepAliveDebounce),this.send("\r\n\r\n"))}startSendingKeepAlives(){this.configuration.keepAliveInterval&&!this.keepAliveInterval&&(this.keepAliveInterval=setInterval((()=>{this.sendKeepAlive(),this.startSendingKeepAlives()}),(e=>{const t=.8*e;return 1e3*(Math.random()*(e-t)+t)})(this.configuration.keepAliveInterval)))}stopSendingKeepAlives(){this.keepAliveInterval&&clearInterval(this.keepAliveInterval),this.keepAliveDebounceTimeout&&clearTimeout(this.keepAliveDebounceTimeout),this.keepAliveInterval=void 0,this.keepAliveDebounceTimeout=void 0}}at.defaultOptions={server:"",connectionTimeout:5,keepAliveInterval:0,keepAliveDebounce:10,traceSip:!0};class ct{constructor(e={}){if(this._publishers={},this._registerers={},this._sessions={},this._subscriptions={},this._state=ne.Stopped,this.unloadListener=()=>{this.stop()},this._stateEventEmitter=new u,this.delegate=e.delegate,this.options=Object.assign(Object.assign(Object.assign(Object.assign(Object.assign({},ct.defaultOptions()),{sipjsId:R(5)}),{uri:new v("sip","anonymous."+R(6),"anonymous.invalid")}),{viaHost:R(12)+".invalid"}),ct.stripUndefinedProperties(e)),this.options.hackIpInContact)if("boolean"==typeof this.options.hackIpInContact&&this.options.hackIpInContact){const e=1,t=254,s=Math.floor(Math.random()*(t-e+1)+e);this.options.viaHost="192.0.2."+s}else this.options.hackIpInContact&&(this.options.viaHost=this.options.hackIpInContact);switch(this.loggerFactory=new pe,this.logger=this.loggerFactory.getLogger("sip.UserAgent"),this.loggerFactory.builtinEnabled=this.options.logBuiltinEnabled,this.loggerFactory.connector=this.options.logConnector,this.options.logLevel){case"error":this.loggerFactory.level=oe.error;break;case"warn":this.loggerFactory.level=oe.warn;break;case"log":this.loggerFactory.level=oe.log;break;case"debug":this.loggerFactory.level=oe.debug}if(this.options.logConfiguration&&(this.logger.log("Configuration:"),Object.keys(this.options).forEach((e=>{const t=this.options[e];switch(e){case"uri":case"sessionDescriptionHandlerFactory":this.logger.log("\xb7 "+e+": "+t);break;case"authorizationPassword":this.logger.log("\xb7 "+e+": NOT SHOWN");break;case"transportConstructor":this.logger.log("\xb7 "+e+": "+t.name);break;default:this.logger.log("\xb7 "+e+": "+JSON.stringify(t))}}))),this.options.transportOptions){const t=this.options.transportOptions,s=t.maxReconnectionAttempts,i=t.reconnectionTimeout;if(void 0!==s){const e='The transport option "maxReconnectionAttempts" as has apparently been specified and has been deprecated. It will no longer be available starting with SIP.js release 0.16.0. Please update accordingly.';this.logger.warn(e)}if(void 0!==i){const e='The transport option "reconnectionTimeout" as has apparently been specified and has been deprecated. It will no longer be available starting with SIP.js release 0.16.0. Please update accordingly.';this.logger.warn(e)}void 0===e.reconnectionDelay&&void 0!==i&&(this.options.reconnectionDelay=i),void 0===e.reconnectionAttempts&&void 0!==s&&(this.options.reconnectionAttempts=s)}if(void 0!==e.reconnectionDelay){const e='The user agent option "reconnectionDelay" as has apparently been specified and has been deprecated. It will no longer be available starting with SIP.js release 0.16.0. Please update accordingly.';this.logger.warn(e)}if(void 0!==e.reconnectionAttempts){const e='The user agent option "reconnectionAttempts" as has apparently been specified and has been deprecated. It will no longer be available starting with SIP.js release 0.16.0. Please update accordingly.';this.logger.warn(e)}this._transport=new this.options.transportConstructor(this.getLogger("sip.Transport"),this.options.transportOptions),this.initTransportCallbacks(),this._contact=this.initContact(),this._userAgentCore=this.initCore(),this.options.autoStart&&this.start()}static makeURI(e){return y.URIParse(e)}static defaultOptions(){return{allowLegacyNotifications:!1,authorizationHa1:"",authorizationPassword:"",authorizationUsername:"",autoStart:!1,autoStop:!0,delegate:{},contactName:"",contactParams:{transport:"ws"},displayName:"",forceRport:!1,hackAllowUnregisteredOptionTags:!1,hackIpInContact:!1,hackViaTcp:!1,logBuiltinEnabled:!0,logConfiguration:!0,logConnector:()=>{},logLevel:"log",noAnswerTimeout:60,preloadedRouteSet:[],reconnectionAttempts:0,reconnectionDelay:4,sendInitialProvisionalResponse:!0,sessionDescriptionHandlerFactory:ot(),sessionDescriptionHandlerFactoryOptions:{},sipExtension100rel:Y.Unsupported,sipExtensionReplaces:Y.Unsupported,sipExtensionExtraSupported:[],sipjsId:"",transportConstructor:at,transportOptions:{},uri:new v("sip","anonymous","anonymous.invalid"),userAgentString:"SIP.js/0.20.0",viaHost:""}}static stripUndefinedProperties(e){return Object.keys(e).reduce(((t,s)=>(void 0!==e[s]&&(t[s]=e[s]),t)),{})}get configuration(){return this.options}get contact(){return this._contact}get state(){return this._state}get stateChange(){return this._stateEventEmitter}get transport(){return this._transport}get userAgentCore(){return this._userAgentCore}getLogger(e,t){return this.loggerFactory.getLogger(e,t)}getLoggerFactory(){return this.loggerFactory}isConnected(){return this.transport.isConnected()}reconnect(){return this.state===ne.Stopped?Promise.reject(new Error("User agent stopped.")):Promise.resolve().then((()=>this.transport.connect()))}start(){if(this.state===ne.Started)return this.logger.warn("User agent already started"),Promise.resolve();if(this.logger.log(`Starting ${this.configuration.uri}`),this.transitionState(ne.Started),this.options.autoStop){const e=!("undefined"==typeof chrome||!chrome.app||!chrome.app.runtime);"undefined"==typeof window||"function"!=typeof window.addEventListener||e||window.addEventListener("unload",this.unloadListener)}return this.transport.connect()}async stop(){if(this.state===ne.Stopped)return this.logger.warn("User agent already stopped"),Promise.resolve();if(this.logger.log(`Stopping ${this.configuration.uri}`),this.transitionState(ne.Stopped),this.options.autoStop){const e=!("undefined"==typeof chrome||!chrome.app||!chrome.app.runtime);"undefined"!=typeof window&&window.removeEventListener&&!e&&window.removeEventListener("unload",this.unloadListener)}const e=Object.assign({},this._publishers),t=Object.assign({},this._registerers),s=Object.assign({},this._sessions),i=Object.assign({},this._subscriptions),r=this.transport,n=this.userAgentCore;this.logger.log("Dispose of registerers");for(const e in t)t[e]&&await t[e].dispose().catch((t=>{throw this.logger.error(t.message),delete this._registerers[e],t}));this.logger.log("Dispose of sessions");for(const e in s)s[e]&&await s[e].dispose().catch((t=>{throw this.logger.error(t.message),delete this._sessions[e],t}));this.logger.log("Dispose of subscriptions");for(const e in i)i[e]&&await i[e].dispose().catch((t=>{throw this.logger.error(t.message),delete this._subscriptions[e],t}));this.logger.log("Dispose of publishers");for(const t in e)e[t]&&await e[t].dispose().catch((e=>{throw this.logger.error(e.message),delete this._publishers[t],e}));this.logger.log("Dispose of transport"),await r.dispose().catch((e=>{throw this.logger.error(e.message),e})),this.logger.log("Dispose of core"),n.dispose()}_makeInviter(e,t){return new X(this,e,t)}attemptReconnection(e=1){const t=this.options.reconnectionAttempts,s=this.options.reconnectionDelay;e>t?this.logger.log("Maximum reconnection attempts reached"):(this.logger.log(`Reconnection attempt ${e} of ${t} - trying`),setTimeout((()=>{this.reconnect().then((()=>{this.logger.log(`Reconnection attempt ${e} of ${t} - succeeded`)})).catch((s=>{this.logger.error(s.message),this.logger.log(`Reconnection attempt ${e} of ${t} - failed`),this.attemptReconnection(++e)}))}),1===e?0:1e3*s))}initContact(){const e=""!==this.options.contactName?this.options.contactName:R(8),t=this.options.contactParams;return{pubGruu:void 0,tempGruu:void 0,uri:new v("sip",e,this.options.viaHost,void 0,t),toString:(e={})=>{const s=e.anonymous||!1,i=e.outbound||!1;let r="<";return r+=s?this.contact.tempGruu||`sip:anonymous@anonymous.invalid;transport=${t.transport?t.transport:"ws"}`:this.contact.pubGruu||this.contact.uri,i&&(r+=";ob"),r+=">",r}}}initCore(){let e=[];e.push("outbound"),this.options.sipExtension100rel===Y.Supported&&e.push("100rel"),this.options.sipExtensionReplaces===Y.Supported&&e.push("replaces"),this.options.sipExtensionExtraSupported&&e.push(...this.options.sipExtensionExtraSupported),this.options.hackAllowUnregisteredOptionTags||(e=e.filter((e=>J[e]))),e=Array.from(new Set(e));const t=e.slice();(this.contact.pubGruu||this.contact.tempGruu)&&t.push("gruu");const s={aor:this.options.uri,contact:this.contact,displayName:this.options.displayName,loggerFactory:this.loggerFactory,hackViaTcp:this.options.hackViaTcp,routeSet:this.options.preloadedRouteSet,supportedOptionTags:e,supportedOptionTagsResponse:t,sipjsId:this.options.sipjsId,userAgentHeaderFieldValue:this.options.userAgentString,viaForceRport:this.options.forceRport,viaHost:this.options.viaHost,authenticationFactory:()=>{const e=this.options.authorizationUsername?this.options.authorizationUsername:this.options.uri.user,t=this.options.authorizationPassword?this.options.authorizationPassword:void 0,s=this.options.authorizationHa1?this.options.authorizationHa1:void 0;return new ve(this.getLoggerFactory(),s,e,t)},transportAccessor:()=>this.transport};return new tt(s,{onInvite:e=>{var t;const s=new z(this,e);if(e.delegate={onCancel:e=>{s._onCancel(e)},onTransportError:e=>{this.logger.error("A transport error has occurred while handling an incoming INVITE request.")}},e.trying(),this.options.sipExtensionReplaces!==Y.Unsupported){const t=e.message.parseHeader("replaces");if(t){const e=t.call_id;if("string"!=typeof e)throw new Error("Type of call id is not string");const i=t.replaces_to_tag;if("string"!=typeof i)throw new Error("Type of to tag is not string");const r=t.replaces_from_tag;if("string"!=typeof r)throw new Error("type of from tag is not string");const n=e+i+r,o=this.userAgentCore.dialogs.get(n);if(!o)return void s.reject({statusCode:481});if(!o.early&&!0===t.early_only)return void s.reject({statusCode:486});const a=this._sessions[e+r]||this._sessions[e+i]||void 0;if(!a)throw new Error("Session does not exist.");s._replacee=a}}if(null===(t=this.delegate)||void 0===t?void 0:t.onInvite)return s.autoSendAnInitialProvisionalResponse?void s.progress().then((()=>{var e;if(void 0===(null===(e=this.delegate)||void 0===e?void 0:e.onInvite))throw new Error("onInvite undefined.");this.delegate.onInvite(s)})):void this.delegate.onInvite(s);s.reject({statusCode:486})},onMessage:e=>{if(this.delegate&&this.delegate.onMessage){const t=new G(e);this.delegate.onMessage(t)}else e.accept()},onNotify:e=>{if(this.delegate&&this.delegate.onNotify){const t=new V(e);this.delegate.onNotify(t)}else this.options.allowLegacyNotifications?e.accept():e.reject({statusCode:481})},onRefer:e=>{this.logger.warn("Received an out of dialog REFER request"),this.delegate&&this.delegate.onReferRequest?this.delegate.onReferRequest(e):e.reject({statusCode:405})},onRegister:e=>{this.logger.warn("Received an out of dialog REGISTER request"),this.delegate&&this.delegate.onRegisterRequest?this.delegate.onRegisterRequest(e):e.reject({statusCode:405})},onSubscribe:e=>{this.logger.warn("Received an out of dialog SUBSCRIBE request"),this.delegate&&this.delegate.onSubscribeRequest?this.delegate.onSubscribeRequest(e):e.reject({statusCode:405})}})}initTransportCallbacks(){this.transport.onConnect=()=>this.onTransportConnect(),this.transport.onDisconnect=e=>this.onTransportDisconnect(e),this.transport.onMessage=e=>this.onTransportMessage(e)}onTransportConnect(){this.state!==ne.Stopped&&this.delegate&&this.delegate.onConnect&&this.delegate.onConnect()}onTransportDisconnect(e){this.state!==ne.Stopped&&(this.delegate&&this.delegate.onDisconnect&&this.delegate.onDisconnect(e),e&&this.options.reconnectionAttempts>0&&this.attemptReconnection())}onTransportMessage(e){const t=st.parseMessage(e,this.getLogger("sip.Parser"));if(!t)return void this.logger.warn("Failed to parse incoming message. Dropping.");if(this.state===ne.Stopped&&t instanceof D)return void this.logger.warn(`Received ${t.method} request while stopped. Dropping.`);const s=()=>{const e=["from","to","call_id","cseq","via"];for(const s of e)if(!t.hasHeader(s))return this.logger.warn(`Missing mandatory header field : ${s}.`),!1;return!0};if(t instanceof D){if(!s())return void this.logger.warn("Request missing mandatory header field. Dropping.");if(!t.toTag&&t.callId.substr(0,5)===this.options.sipjsId)return void this.userAgentCore.replyStateless(t,{statusCode:482});const e=C(t.body),i=t.getHeader("content-length");if(i&&e<Number(i))return void this.userAgentCore.replyStateless(t,{statusCode:400})}if(t instanceof H){if(!s())return void this.logger.warn("Response missing mandatory header field. Dropping.");if(t.getHeaders("via").length>1)return void this.logger.warn("More than one Via header field present in the response. Dropping.");if(t.via.host!==this.options.viaHost||void 0!==t.via.port)return void this.logger.warn("Via sent-by in the response does not match UA Via host value. Dropping.");const e=C(t.body),i=t.getHeader("content-length");if(i&&e<Number(i))return void this.logger.warn("Message body length is lower than the value in Content-Length header field. Dropping.")}if(t instanceof D)this.userAgentCore.receiveIncomingRequestFromTransport(t);else{if(!(t instanceof H))throw new Error("Invalid message type.");this.userAgentCore.receiveIncomingResponseFromTransport(t)}}transitionState(e,t){const s=()=>{throw new Error(`Invalid state transition from ${this._state} to ${e}`)};switch(this._state){case ne.Started:e!==ne.Stopped&&s();break;case ne.Stopped:e!==ne.Started&&s();break;default:throw new Error("Unknown state.")}this.logger.log(`Transitioned from ${this._state} to ${e}`),this._state=e,this._stateEventEmitter.emit(this._state)}}class ht extends Ce{constructor(e,t,s){super(Se,e,t,s)}}class dt extends He{constructor(e,t,s){super(De,e.userAgentCore,t,s)}}const lt=(e,t)=>{const s=[],i=e.split(/\r\n/);let r;for(let e=0;e<i.length;){const n=i[e];if(/^m=(?:audio|video)/.test(n))r={index:e,stripped:[]},s.push(r);else if(r){const s=/^a=rtpmap:(\d+) ([^/]+)\//.exec(n);if(s&&t===s[2]){i.splice(e,1),r.stripped.push(s[1]);continue}}e++}for(const e of s){const t=i[e.index].split(" ");for(let s=3;s<t.length;)-1===e.stripped.indexOf(t[s])?s++:t.splice(s,1);i[e.index]=t.join(" ")}return i.join("\r\n")};function gt(e){return e.sdp=(e.sdp||"").replace(/^a=candidate:\d+ \d+ tcp .*?\r\n/gim,""),Promise.resolve(e)}function ut(e){return e.sdp=lt(e.sdp||"","telephone-event"),Promise.resolve(e)}function pt(e){return e.sdp=(e.sdp||"").replace(/^(a=imageattr:.*?)(x|y)=\[0-/gm,"$1$2=[1:"),Promise.resolve(e)}function ft(e){return e.sdp=lt(e.sdp||"","G722"),Promise.resolve(e)}function mt(e){return t=>(t.sdp=lt(t.sdp||"",e),Promise.resolve(t))}function vt(e){return e.sdp=((e,t)=>{const s=new RegExp("m="+t+".*$","gm"),i=new RegExp("^a=group:.*$","gm");if(s.test(e)){let s;const r=(e=e.split(/^m=/gm).filter((e=>{if(e.substr(0,t.length)===t){if(s=e.match(/^a=mid:.*$/gm),s){const e=s[0].match(/:.+$/g);e&&(s=e[0].substr(1))}return!1}return!0})).join("m=")).match(i);if(r&&1===r.length){let t=r[0];const n=new RegExp(" *"+s+"[^ ]*","g");t=t.replace(n,""),e=e.split(i).join(t)}}return e})(e.sdp||"","video"),Promise.resolve(e)}function wt(e){let t=e.sdp||"";if(-1===t.search(/^a=mid.*$/gm)){const s=t.match(/^m=.*$/gm),i=t.split(/^m=.*$/gm);s&&s.forEach(((e,t)=>{s[t]=e+"\na=mid:"+t})),i.forEach(((e,t)=>{s&&s[t]&&(i[t]=e+s[t])})),t=i.join(""),e.sdp=t}return Promise.resolve(e)}function bt(e){if(!e.sdp||!e.type)throw new Error("Invalid SDP");let t=e.sdp;const s=e.type;return t&&(/a=(sendrecv|sendonly|recvonly|inactive)/.test(t)?(t=t.replace(/a=sendrecv\r\n/g,"a=sendonly\r\n"),t=t.replace(/a=recvonly\r\n/g,"a=inactive\r\n")):t=t.replace(/(m=[^\r]*\r\n)/g,"$1a=sendonly\r\n")),Promise.resolve({sdp:t,type:s})}class Tt{constructor(e,t={}){this.attemptingReconnection=!1,this.connectRequested=!1,this.held=!1,this.muted=!1,this.registerer=void 0,this.registerRequested=!1,this.session=void 0,this.delegate=t.delegate,this.options=Object.assign({},t);const s=Object.assign({},t.userAgentOptions);if(s.transportConstructor||(s.transportConstructor=at),s.transportOptions||(s.transportOptions={server:e}),!s.uri&&t.aor){const e=ct.makeURI(t.aor);if(!e)throw new Error(`Failed to create valid URI from ${t.aor}`);s.uri=e}this.userAgent=new ct(s),this.userAgent.delegate={onConnect:()=>{this.logger.log(`[${this.id}] Connected`),this.delegate&&this.delegate.onServerConnect&&this.delegate.onServerConnect(),this.registerer&&this.registerRequested&&(this.logger.log(`[${this.id}] Registering...`),this.registerer.register().catch((e=>{this.logger.error(`[${this.id}] Error occurred registering after connection with server was obtained.`),this.logger.error(e.toString())})))},onDisconnect:e=>{this.logger.log(`[${this.id}] Disconnected`),this.delegate&&this.delegate.onServerDisconnect&&this.delegate.onServerDisconnect(e),this.session&&(this.logger.log(`[${this.id}] Hanging up...`),this.hangup().catch((e=>{this.logger.error(`[${this.id}] Error occurred hanging up call after connection with server was lost.`),this.logger.error(e.toString())}))),this.registerer&&(this.logger.log(`[${this.id}] Unregistering...`),this.registerer.unregister().catch((e=>{this.logger.error(`[${this.id}] Error occurred unregistering after connection with server was lost.`),this.logger.error(e.toString())}))),e&&this.attemptReconnection()},onInvite:e=>{if(this.logger.log(`[${this.id}] Received INVITE`),this.session)return this.logger.warn(`[${this.id}] Session already in progress, rejecting INVITE...`),void e.reject().then((()=>{this.logger.log(`[${this.id}] Rejected INVITE`)})).catch((e=>{this.logger.error(`[${this.id}] Failed to reject INVITE`),this.logger.error(e.toString())}));const t={sessionDescriptionHandlerOptions:{constraints:this.constraints}};this.initSession(e,t),this.delegate&&this.delegate.onCallReceived?this.delegate.onCallReceived():(this.logger.warn(`[${this.id}] No handler available, rejecting INVITE...`),e.reject().then((()=>{this.logger.log(`[${this.id}] Rejected INVITE`)})).catch((e=>{this.logger.error(`[${this.id}] Failed to reject INVITE`),this.logger.error(e.toString())})))},onMessage:e=>{e.accept().then((()=>{this.delegate&&this.delegate.onMessageReceived&&this.delegate.onMessageReceived(e.request.body)}))}},this.logger=this.userAgent.getLogger("sip.SimpleUser"),window.addEventListener("online",(()=>{this.logger.log(`[${this.id}] Online`),this.attemptReconnection()}))}get id(){return this.options.userAgentOptions&&this.options.userAgentOptions.displayName||"Anonymous"}get localMediaStream(){var e;const t=null===(e=this.session)||void 0===e?void 0:e.sessionDescriptionHandler;if(t){if(!(t instanceof nt))throw new Error("Session description handler not instance of web SessionDescriptionHandler");return t.localMediaStream}}get remoteMediaStream(){var e;const t=null===(e=this.session)||void 0===e?void 0:e.sessionDescriptionHandler;if(t){if(!(t instanceof nt))throw new Error("Session description handler not instance of web SessionDescriptionHandler");return t.remoteMediaStream}}get localAudioTrack(){var e;return null===(e=this.localMediaStream)||void 0===e?void 0:e.getTracks().find((e=>"audio"===e.kind))}get localVideoTrack(){var e;return null===(e=this.localMediaStream)||void 0===e?void 0:e.getTracks().find((e=>"video"===e.kind))}get remoteAudioTrack(){var e;return null===(e=this.remoteMediaStream)||void 0===e?void 0:e.getTracks().find((e=>"audio"===e.kind))}get remoteVideoTrack(){var e;return null===(e=this.remoteMediaStream)||void 0===e?void 0:e.getTracks().find((e=>"video"===e.kind))}connect(){return this.logger.log(`[${this.id}] Connecting UserAgent...`),this.connectRequested=!0,this.userAgent.state!==ne.Started?this.userAgent.start():this.userAgent.reconnect()}disconnect(){return this.logger.log(`[${this.id}] Disconnecting UserAgent...`),this.connectRequested=!1,this.userAgent.stop()}isConnected(){return this.userAgent.isConnected()}register(e,t){return this.logger.log(`[${this.id}] Registering UserAgent...`),this.registerRequested=!0,this.registerer||(this.registerer=new he(this.userAgent,e),this.registerer.stateChange.addListener((e=>{switch(e){case te.Initial:break;case te.Registered:this.delegate&&this.delegate.onRegistered&&this.delegate.onRegistered();break;case te.Unregistered:this.delegate&&this.delegate.onUnregistered&&this.delegate.onUnregistered();break;case te.Terminated:this.registerer=void 0;break;default:throw new Error("Unknown registerer state.")}}))),this.registerer.register(t).then((()=>{}))}unregister(e){return this.logger.log(`[${this.id}] Unregistering UserAgent...`),this.registerRequested=!1,this.registerer?this.registerer.unregister(e).then((()=>{})):Promise.resolve()}call(e,t,s){if(this.logger.log(`[${this.id}] Beginning Session...`),this.session)return Promise.reject(new Error("Session already exists."));const i=ct.makeURI(e);if(!i)return Promise.reject(new Error(`Failed to create a valid URI from "${e}"`));t||(t={}),t.sessionDescriptionHandlerOptions||(t.sessionDescriptionHandlerOptions={}),t.sessionDescriptionHandlerOptions.constraints||(t.sessionDescriptionHandlerOptions.constraints=this.constraints);const r=new X(this.userAgent,i,t);return this.sendInvite(r,t,s).then((()=>{}))}hangup(){return this.logger.log(`[${this.id}] Hangup...`),this.terminate()}answer(e){return this.logger.log(`[${this.id}] Accepting Invitation...`),this.session?this.session instanceof z?(e||(e={}),e.sessionDescriptionHandlerOptions||(e.sessionDescriptionHandlerOptions={}),e.sessionDescriptionHandlerOptions.constraints||(e.sessionDescriptionHandlerOptions.constraints=this.constraints),this.session.accept(e)):Promise.reject(new Error("Session not instance of Invitation.")):Promise.reject(new Error("Session does not exist."))}decline(){return this.logger.log(`[${this.id}] rejecting Invitation...`),this.session?this.session instanceof z?this.session.reject():Promise.reject(new Error("Session not instance of Invitation.")):Promise.reject(new Error("Session does not exist."))}hold(){return this.logger.log(`[${this.id}] holding session...`),this.setHold(!0)}unhold(){return this.logger.log(`[${this.id}] unholding session...`),this.setHold(!1)}isHeld(){return this.held}mute(){this.logger.log(`[${this.id}] disabling media tracks...`),this.setMute(!0)}unmute(){this.logger.log(`[${this.id}] enabling media tracks...`),this.setMute(!1)}isMuted(){return this.muted}sendDTMF(e){if(this.logger.log(`[${this.id}] sending DTMF...`),!/^[0-9A-D#*,]$/.exec(e))return Promise.reject(new Error("Invalid DTMF tone."));if(!this.session)return Promise.reject(new Error("Session does not exist."));this.logger.log(`[${this.id}] Sending DTMF tone: ${e}`);const t={body:{contentDisposition:"render",contentType:"application/dtmf-relay",content:"Signal="+e+"\r\nDuration=2000"}};return this.session.info({requestOptions:t}).then((()=>{}))}message(e,t){this.logger.log(`[${this.id}] sending message...`);const s=ct.makeURI(e);return s?new Q(this.userAgent,s,t).message():Promise.reject(new Error(`Failed to create a valid URI from "${e}"`))}get constraints(){var e;let t={audio:!0,video:!1};return(null===(e=this.options.media)||void 0===e?void 0:e.constraints)&&(t=Object.assign({},this.options.media.constraints)),t}attemptReconnection(e=1){const t=this.options.reconnectionAttempts||3,s=this.options.reconnectionDelay||4;this.connectRequested?(this.attemptingReconnection&&this.logger.log(`[${this.id}] Reconnection attempt already in progress`),e>t?this.logger.log(`[${this.id}] Reconnection maximum attempts reached`):(1===e?this.logger.log(`[${this.id}] Reconnection attempt ${e} of ${t} - trying`):this.logger.log(`[${this.id}] Reconnection attempt ${e} of ${t} - trying in ${s} seconds`),this.attemptingReconnection=!0,setTimeout((()=>{if(!this.connectRequested)return this.logger.log(`[${this.id}] Reconnection attempt ${e} of ${t} - aborted`),void(this.attemptingReconnection=!1);this.userAgent.reconnect().then((()=>{this.logger.log(`[${this.id}] Reconnection attempt ${e} of ${t} - succeeded`),this.attemptingReconnection=!1})).catch((s=>{this.logger.log(`[${this.id}] Reconnection attempt ${e} of ${t} - failed`),this.logger.error(s.message),this.attemptingReconnection=!1,this.attemptReconnection(++e)}))}),1===e?0:1e3*s))):this.logger.log(`[${this.id}] Reconnection not currently desired`)}cleanupMedia(){this.options.media&&(this.options.media.local&&this.options.media.local.video&&(this.options.media.local.video.srcObject=null,this.options.media.local.video.pause()),this.options.media.remote&&(this.options.media.remote.audio&&(this.options.media.remote.audio.srcObject=null,this.options.media.remote.audio.pause()),this.options.media.remote.video&&(this.options.media.remote.video.srcObject=null,this.options.media.remote.video.pause())))}enableReceiverTracks(e){if(!this.session)throw new Error("Session does not exist.");const t=this.session.sessionDescriptionHandler;if(!(t instanceof nt))throw new Error("Session's session description handler not instance of SessionDescriptionHandler.");const s=t.peerConnection;if(!s)throw new Error("Peer connection closed.");s.getReceivers().forEach((t=>{t.track&&(t.track.enabled=e)}))}enableSenderTracks(e){if(!this.session)throw new Error("Session does not exist.");const t=this.session.sessionDescriptionHandler;if(!(t instanceof nt))throw new Error("Session's session description handler not instance of SessionDescriptionHandler.");const s=t.peerConnection;if(!s)throw new Error("Peer connection closed.");s.getSenders().forEach((t=>{t.track&&(t.track.enabled=e)}))}initSession(e,t){this.session=e,this.delegate&&this.delegate.onCallCreated&&this.delegate.onCallCreated(),this.session.stateChange.addListener((t=>{if(this.session===e)switch(this.logger.log(`[${this.id}] session state changed to ${t}`),t){case W.Initial:case W.Establishing:break;case W.Established:this.setupLocalMedia(),this.setupRemoteMedia(),this.delegate&&this.delegate.onCallAnswered&&this.delegate.onCallAnswered();break;case W.Terminating:case W.Terminated:this.session=void 0,this.cleanupMedia(),this.delegate&&this.delegate.onCallHangup&&this.delegate.onCallHangup();break;default:throw new Error("Unknown session state.")}})),this.session.delegate={onInfo:e=>{var t;if(void 0===(null===(t=this.delegate)||void 0===t?void 0:t.onCallDTMFReceived))return void e.reject();const s=e.request.getHeader("content-type");if(!s||!/^application\/dtmf-relay/i.exec(s))return void e.reject();const i=e.request.body.split("\r\n",2);if(2!==i.length)return void e.reject();let r;const n=/^(Signal\s*?=\s*?)([0-9A-D#*]{1})(\s)?.*/;if(n.test(i[0])&&(r=i[0].replace(n,"$2")),!r)return void e.reject();let o;const a=/^(Duration\s?=\s?)([0-9]{1,4})(\s)?.*/;a.test(i[1])&&(o=parseInt(i[1].replace(a,"$2"),10)),o?e.accept().then((()=>{if(this.delegate&&this.delegate.onCallDTMFReceived){if(!r||!o)throw new Error("Tone or duration undefined.");this.delegate.onCallDTMFReceived(r,o)}})).catch((e=>{this.logger.error(e.message)})):e.reject()},onRefer:e=>{e.accept().then((()=>this.sendInvite(e.makeInviter(t),t))).catch((e=>{this.logger.error(e.message)}))}}}sendInvite(e,t,s){return this.initSession(e,t),e.invite(s).then((()=>{this.logger.log(`[${this.id}] sent INVITE`)}))}setHold(e){if(!this.session)return Promise.reject(new Error("Session does not exist."));const t=this.session;if(this.held===e)return Promise.resolve();if(!(this.session.sessionDescriptionHandler instanceof nt))throw new Error("Session's session description handler not instance of SessionDescriptionHandler.");const s={requestDelegate:{onAccept:()=>{this.held=e,this.enableReceiverTracks(!this.held),this.enableSenderTracks(!this.held&&!this.muted),this.delegate&&this.delegate.onCallHold&&this.delegate.onCallHold(this.held)},onReject:()=>{this.logger.warn(`[${this.id}] re-invite request was rejected`),this.enableReceiverTracks(!this.held),this.enableSenderTracks(!this.held&&!this.muted),this.delegate&&this.delegate.onCallHold&&this.delegate.onCallHold(this.held)}}},i=t.sessionDescriptionHandlerOptionsReInvite;return i.hold=e,t.sessionDescriptionHandlerOptionsReInvite=i,this.session.invite(s).then((()=>{this.enableReceiverTracks(!e),this.enableSenderTracks(!e&&!this.muted)})).catch((e=>{throw e instanceof a&&this.logger.error(`[${this.id}] A hold request is already in progress.`),e}))}setMute(e){this.session?this.session.state===W.Established?(this.muted=e,this.enableSenderTracks(!this.held&&!this.muted)):this.logger.warn(`[${this.id}] An established session is required to enable/disable media tracks`):this.logger.warn(`[${this.id}] A session is required to enabled/disable media tracks`)}setupLocalMedia(){var e,t;if(!this.session)throw new Error("Session does not exist.");const s=null===(t=null===(e=this.options.media)||void 0===e?void 0:e.local)||void 0===t?void 0:t.video;if(s){const e=this.localMediaStream;if(!e)throw new Error("Local media stream undefiend.");s.srcObject=e,s.volume=0,s.play().catch((e=>{this.logger.error(`[${this.id}] Failed to play local media`),this.logger.error(e.message)}))}}setupRemoteMedia(){var e,t,s,i;if(!this.session)throw new Error("Session does not exist.");const r=(null===(t=null===(e=this.options.media)||void 0===e?void 0:e.remote)||void 0===t?void 0:t.video)||(null===(i=null===(s=this.options.media)||void 0===s?void 0:s.remote)||void 0===i?void 0:i.audio);if(r){const e=this.remoteMediaStream;if(!e)throw new Error("Remote media stream undefiend.");r.autoplay=!0,r.srcObject=e,r.play().catch((e=>{this.logger.error(`[${this.id}] Failed to play remote media`),this.logger.error(e.message)})),e.onaddtrack=()=>{this.logger.log(`[${this.id}] Remote media onaddtrack`),r.load(),r.play().catch((e=>{this.logger.error(`[${this.id}] Failed to play remote media`),this.logger.error(e.message)}))}}}terminate(){if(this.logger.log(`[${this.id}] Terminating...`),!this.session)return Promise.reject(new Error("Session does not exist."));switch(this.session.state){case W.Initial:if(this.session instanceof X)return this.session.cancel().then((()=>{this.logger.log(`[${this.id}] Inviter never sent INVITE (canceled)`)}));if(this.session instanceof z)return this.session.reject().then((()=>{this.logger.log(`[${this.id}] Invitation rejected (sent 480)`)}));throw new Error("Unknown session type.");case W.Establishing:if(this.session instanceof X)return this.session.cancel().then((()=>{this.logger.log(`[${this.id}] Inviter canceled (sent CANCEL)`)}));if(this.session instanceof z)return this.session.reject().then((()=>{this.logger.log(`[${this.id}] Invitation rejected (sent 480)`)}));throw new Error("Unknown session type.");case W.Established:return this.session.bye().then((()=>{this.logger.log(`[${this.id}] Session ended (sent BYE)`)}));case W.Terminating:case W.Terminated:break;default:throw new Error("Unknown state")}return this.logger.log(`[${this.id}] Terminating in state ${this.session.state}, no action taken`),Promise.resolve()}}const yt=r,St="sip.js";return t})()}));
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?t(exports):"function"==typeof define&&define.amd?define(["exports"],t):t((e=e||self).strophe={})}(this,function(e){"use strict";var t="undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{};function m(e){return(m="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function a(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function r(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function i(e,t,n){return t&&r(e.prototype,t),n&&r(e,n),e}function o(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&n(e,t)}function s(e){return(s=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function n(e,t){return(n=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function c(e,t){return!t||"object"!=typeof t&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function u(n){var r=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],function(){})),!0}catch(e){return!1}}();return function(){var e,t=s(n);return c(this,r?(e=s(this).constructor,Reflect.construct(t,arguments,e)):t.apply(this,arguments))}}function h(e){return function(e){if(Array.isArray(e))return l(e)}(e)||function(e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e))return Array.from(e)}(e)||function(e,t){if(!e)return;if("string"==typeof e)return l(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return l(e,t)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function l(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}!function(){var e=t.WebSocket;if(void 0===e)try{e=require("ws")}catch(e){throw new Error('You must install the "ws" package to use Strophe in nodejs.')}}();var d=function(){var e=t.DOMParser;if(void 0===e)try{e=require("xmldom").DOMParser}catch(e){throw new Error('You must install the "xmldom" package to use Strophe in nodejs.')}return e}();function _(){if("undefined"==typeof document)try{return(new(require("xmldom").DOMImplementation)).createDocument("jabber:client","strophe",null)}catch(e){throw new Error('You must install the "xmldom" package to use Strophe in nodejs.')}if(void 0===document.implementation.createDocument||document.implementation.createDocument&&document.documentMode&&document.documentMode<10){var e=function(){for(var e=["Msxml2.DOMDocument.6.0","Msxml2.DOMDocument.5.0","Msxml2.DOMDocument.4.0","MSXML2.DOMDocument.3.0","MSXML2.DOMDocument","MSXML.DOMDocument","Microsoft.XMLDOM"],t=0;t<e.length;t++)try{return new ActiveXObject(e[t])}catch(e){}}();return e.appendChild(e.createElement("strophe")),e}return document.implementation.createDocument("jabber:client","strophe",null)}function f(e,t){var n=(65535&e)+(65535&t);return(e>>16)+(t>>16)+(n>>16)<<16|65535&n}function p(e){if("string"!=typeof e)throw new Error("str2binl was passed a non-string");for(var t=[],n=0;n<8*e.length;n+=8)t[n>>5]|=(255&e.charCodeAt(n/8))<<n%32;return t}function g(e,t,n,r,s,i){return f((o=f(f(t,e),f(r,i)))<<(a=s)|o>>>32-a,n);var o,a}function v(e,t,n,r,s,i,o){return g(t&n|~t&r,e,t,s,i,o)}function y(e,t,n,r,s,i,o){return g(t&r|n&~r,e,t,s,i,o)}function S(e,t,n,r,s,i,o){return g(t^n^r,e,t,s,i,o)}function b(e,t,n,r,s,i,o){return g(n^(t|~r),e,t,s,i,o)}function T(e,t){e[t>>5]|=128<<t%32,e[14+(t+64>>>9<<4)]=t;for(var n,r,s,i,o=1732584193,a=-271733879,c=-1732584194,u=271733878,h=0;h<e.length;h+=16)o=v(n=o,r=a,s=c,i=u,e[h+0],7,-680876936),u=v(u,o,a,c,e[h+1],12,-389564586),c=v(c,u,o,a,e[h+2],17,606105819),a=v(a,c,u,o,e[h+3],22,-1044525330),o=v(o,a,c,u,e[h+4],7,-176418897),u=v(u,o,a,c,e[h+5],12,1200080426),c=v(c,u,o,a,e[h+6],17,-1473231341),a=v(a,c,u,o,e[h+7],22,-45705983),o=v(o,a,c,u,e[h+8],7,1770035416),u=v(u,o,a,c,e[h+9],12,-1958414417),c=v(c,u,o,a,e[h+10],17,-42063),a=v(a,c,u,o,e[h+11],22,-1990404162),o=v(o,a,c,u,e[h+12],7,1804603682),u=v(u,o,a,c,e[h+13],12,-40341101),c=v(c,u,o,a,e[h+14],17,-1502002290),a=v(a,c,u,o,e[h+15],22,1236535329),o=y(o,a,c,u,e[h+1],5,-165796510),u=y(u,o,a,c,e[h+6],9,-1069501632),c=y(c,u,o,a,e[h+11],14,643717713),a=y(a,c,u,o,e[h+0],20,-373897302),o=y(o,a,c,u,e[h+5],5,-701558691),u=y(u,o,a,c,e[h+10],9,38016083),c=y(c,u,o,a,e[h+15],14,-660478335),a=y(a,c,u,o,e[h+4],20,-405537848),o=y(o,a,c,u,e[h+9],5,568446438),u=y(u,o,a,c,e[h+14],9,-1019803690),c=y(c,u,o,a,e[h+3],14,-187363961),a=y(a,c,u,o,e[h+8],20,1163531501),o=y(o,a,c,u,e[h+13],5,-1444681467),u=y(u,o,a,c,e[h+2],9,-51403784),c=y(c,u,o,a,e[h+7],14,1735328473),a=y(a,c,u,o,e[h+12],20,-1926607734),o=S(o,a,c,u,e[h+5],4,-378558),u=S(u,o,a,c,e[h+8],11,-2022574463),c=S(c,u,o,a,e[h+11],16,1839030562),a=S(a,c,u,o,e[h+14],23,-35309556),o=S(o,a,c,u,e[h+1],4,-1530992060),u=S(u,o,a,c,e[h+4],11,1272893353),c=S(c,u,o,a,e[h+7],16,-155497632),a=S(a,c,u,o,e[h+10],23,-1094730640),o=S(o,a,c,u,e[h+13],4,681279174),u=S(u,o,a,c,e[h+0],11,-358537222),c=S(c,u,o,a,e[h+3],16,-722521979),a=S(a,c,u,o,e[h+6],23,76029189),o=S(o,a,c,u,e[h+9],4,-640364487),u=S(u,o,a,c,e[h+12],11,-421815835),c=S(c,u,o,a,e[h+15],16,530742520),a=S(a,c,u,o,e[h+2],23,-995338651),o=b(o,a,c,u,e[h+0],6,-198630844),u=b(u,o,a,c,e[h+7],10,1126891415),c=b(c,u,o,a,e[h+14],15,-1416354905),a=b(a,c,u,o,e[h+5],21,-57434055),o=b(o,a,c,u,e[h+12],6,1700485571),u=b(u,o,a,c,e[h+3],10,-1894986606),c=b(c,u,o,a,e[h+10],15,-1051523),a=b(a,c,u,o,e[h+1],21,-2054922799),o=b(o,a,c,u,e[h+8],6,1873313359),u=b(u,o,a,c,e[h+15],10,-30611744),c=b(c,u,o,a,e[h+6],15,-1560198380),a=b(a,c,u,o,e[h+13],21,1309151649),o=b(o,a,c,u,e[h+4],6,-145523070),u=b(u,o,a,c,e[h+11],10,-1120210379),c=b(c,u,o,a,e[h+2],15,718787259),a=b(a,c,u,o,e[h+9],21,-343485551),o=f(o,n),a=f(a,r),c=f(c,s),u=f(u,i);return[o,a,c,u]}var x={hexdigest:function(e){return function(e){for(var t="0123456789abcdef",n="",r=0;r<4*e.length;r++)n+=t.charAt(e[r>>2]>>r%4*8+4&15)+t.charAt(e[r>>2]>>r%4*8&15);return n}(T(p(e),8*e.length))},hash:function(e){return function(e){for(var t="",n=0;n<32*e.length;n+=8)t+=String.fromCharCode(e[n>>5]>>>n%32&255);return t}(T(p(e),8*e.length))}};function N(e,t){e[t>>5]|=128<<24-t%32,e[15+(t+64>>9<<4)]=t;for(var n,r,s,i,o,a,c,u,h=new Array(80),l=1732584193,d=-271733879,_=-1732584194,f=271733878,m=-1009589776,p=0;p<e.length;p+=16){for(s=l,i=d,o=_,a=f,c=m,n=0;n<80;n++)h[n]=n<16?e[p+n]:C(h[n-3]^h[n-8]^h[n-14]^h[n-16],1),r=w(w(C(l,5),function(e,t,n,r){if(e<20)return t&n|~t&r;if(e<40)return t^n^r;if(e<60)return t&n|t&r|n&r;return t^n^r}(n,d,_,f)),w(w(m,h[n]),(u=n)<20?1518500249:u<40?1859775393:u<60?-1894007588:-899497514)),m=f,f=_,_=C(d,30),d=l,l=r;l=w(l,s),d=w(d,i),_=w(_,o),f=w(f,a),m=w(m,c)}return[l,d,_,f,m]}function A(e,t){var n=k(e);16<n.length&&(n=N(n,8*e.length));for(var r=new Array(16),s=new Array(16),i=0;i<16;i++)r[i]=909522486^n[i],s[i]=1549556828^n[i];var o=N(r.concat(k(t)),512+8*t.length);return N(s.concat(o),672)}function w(e,t){var n=(65535&e)+(65535&t);return(e>>16)+(t>>16)+(n>>16)<<16|65535&n}function C(e,t){return e<<t|e>>>32-t}function k(e){for(var t=[],n=0;n<8*e.length;n+=8)t[n>>5]|=(255&e.charCodeAt(n/8))<<24-n%32;return t}function E(e){for(var t,n,r="",s=0;s<4*e.length;s+=3)for(t=(e[s>>2]>>8*(3-s%4)&255)<<16|(e[s+1>>2]>>8*(3-(s+1)%4)&255)<<8|e[s+2>>2]>>8*(3-(s+2)%4)&255,n=0;n<4;n++)8*s+6*n>32*e.length?r+="=":r+="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(t>>6*(3-n)&63);return r}function O(e){for(var t="",n=0;n<32*e.length;n+=8)t+=String.fromCharCode(e[n>>5]>>>24-n%32&255);return t}var I={b64_hmac_sha1:function(e,t){return E(A(e,t))},b64_sha1:function(e){return E(N(k(e),8*e.length))},binb2str:O,core_hmac_sha1:A,str_hmac_sha1:function(e,t){return O(A(e,t))},str_sha1:function(e){return O(N(k(e),8*e.length))}},R={utf16to8:function(e){for(var t,n="",r=e.length,s=0;s<r;s++)0<=(t=e.charCodeAt(s))&&t<=127?n+=e.charAt(s):(2047<t?(n+=String.fromCharCode(224|t>>12&15),n+=String.fromCharCode(128|t>>6&63)):n+=String.fromCharCode(192|t>>6&31),n+=String.fromCharCode(128|t>>0&63));return n},addCookies:function(e){for(var t in e=e||{}){var n,r,s,i,o,a;Object.prototype.hasOwnProperty.call(e,t)&&(s=r=n="",o="object"===m(i=e[t]),a=escape(unescape(o?i.value:i)),o&&(n=i.expires?";expires="+i.expires:"",r=i.domain?";domain="+i.domain:"",s=i.path?";path="+i.path:""),document.cookie=t+"="+a+n+r+s)}}};var H={atob:function(e){if((e=(e="".concat(e)).replace(/[ \t\n\f\r]/g,"")).length%4==0&&(e=e.replace(/==?$/,"")),e.length%4==1||/[^+/0-9A-Za-z]/.test(e))return null;for(var t="",n=0,r=0,s=0;s<e.length;s++)n<<=6,n|=function(e){if(/[A-Z]/.test(e))return e.charCodeAt(0)-"A".charCodeAt(0);if(/[a-z]/.test(e))return e.charCodeAt(0)-"a".charCodeAt(0)+26;if(/[0-9]/.test(e))return e.charCodeAt(0)-"0".charCodeAt(0)+52;if("+"===e)return 62;if("/"!==e)return;return 63}(e[s]),24===(r+=6)&&(t+=String.fromCharCode((16711680&n)>>16),t+=String.fromCharCode((65280&n)>>8),t+=String.fromCharCode(255&n),n=r=0);return 12===r?(n>>=4,t+=String.fromCharCode(n)):18===r&&(n>>=2,t+=String.fromCharCode((65280&n)>>8),t+=String.fromCharCode(255&n)),t},btoa:function(e){for(e="".concat(e),n=0;n<e.length;n++)if(255<e.charCodeAt(n))return null;for(var t="",n=0;n<e.length;n+=3){var r=[void 0,void 0,void 0,void 0];r[0]=e.charCodeAt(n)>>2,r[1]=(3&e.charCodeAt(n))<<4,e.length>n+1&&(r[1]|=e.charCodeAt(n+1)>>4,r[2]=(15&e.charCodeAt(n+1))<<2),e.length>n+2&&(r[2]|=e.charCodeAt(n+2)>>6,r[3]=63&e.charCodeAt(n+2));for(var s=0;s<r.length;s++)void 0===r[s]?t+="=":t+=function(e){if(e<26)return String.fromCharCode(e+"A".charCodeAt(0));if(e<52)return String.fromCharCode(e-26+"a".charCodeAt(0));if(e<62)return String.fromCharCode(e-52+"0".charCodeAt(0));if(62===e)return"+";if(63!==e)return;return"/"}(r[s])}return t}};function M(e,t){return new F.Builder(e,t)}function L(e){return new F.Builder("message",e)}function q(e){return new F.Builder("iq",e)}function D(e){return new F.Builder("presence",e)}var F={VERSION:"1.4.0",NS:{HTTPBIND:"http://jabber.org/protocol/httpbind",BOSH:"urn:xmpp:xbosh",CLIENT:"jabber:client",AUTH:"jabber:iq:auth",ROSTER:"jabber:iq:roster",PROFILE:"jabber:iq:profile",DISCO_INFO:"http://jabber.org/protocol/disco#info",DISCO_ITEMS:"http://jabber.org/protocol/disco#items",MUC:"http://jabber.org/protocol/muc",SASL:"urn:ietf:params:xml:ns:xmpp-sasl",STREAM:"http://etherx.jabber.org/streams",FRAMING:"urn:ietf:params:xml:ns:xmpp-framing",BIND:"urn:ietf:params:xml:ns:xmpp-bind",SESSION:"urn:ietf:params:xml:ns:xmpp-session",VERSION:"jabber:iq:version",STANZAS:"urn:ietf:params:xml:ns:xmpp-stanzas",XHTML_IM:"http://jabber.org/protocol/xhtml-im",XHTML:"http://www.w3.org/1999/xhtml"},XHTML:{tags:["a","blockquote","br","cite","em","img","li","ol","p","span","strong","ul","body"],attributes:{a:["href"],blockquote:["style"],br:[],cite:["style"],em:[],img:["src","alt","style","height","width"],li:["style"],ol:["style"],p:["style"],span:["style"],strong:[],ul:["style"],body:[]},css:["background-color","color","font-family","font-size","font-style","font-weight","margin-left","margin-right","text-align","text-decoration"],validTag:function(e){for(var t=0;t<F.XHTML.tags.length;t++)if(e===F.XHTML.tags[t])return!0;return!1},validAttribute:function(e,t){if(void 0!==F.XHTML.attributes[e]&&0<F.XHTML.attributes[e].length)for(var n=0;n<F.XHTML.attributes[e].length;n++)if(t===F.XHTML.attributes[e][n])return!0;return!1},validCSS:function(e){for(var t=0;t<F.XHTML.css.length;t++)if(e===F.XHTML.css[t])return!0;return!1}},Status:{ERROR:0,CONNECTING:1,CONNFAIL:2,AUTHENTICATING:3,AUTHFAIL:4,CONNECTED:5,DISCONNECTED:6,DISCONNECTING:7,ATTACHED:8,REDIRECT:9,CONNTIMEOUT:10,BINDREQUIRED:11,ATTACHFAIL:12},ErrorCondition:{BAD_FORMAT:"bad-format",CONFLICT:"conflict",MISSING_JID_NODE:"x-strophe-bad-non-anon-jid",NO_AUTH_MECH:"no-auth-mech",UNKNOWN_REASON:"unknown"},LogLevel:{DEBUG:0,INFO:1,WARN:2,ERROR:3,FATAL:4},ElementType:{NORMAL:1,TEXT:3,CDATA:4,FRAGMENT:11},TIMEOUT:1.1,SECONDARY_TIMEOUT:.1,addNamespace:function(e,t){F.NS[e]=t},forEachChild:function(e,t,n){for(var r=0;r<e.childNodes.length;r++){var s=e.childNodes[r];s.nodeType!==F.ElementType.NORMAL||t&&!this.isTagEqual(s,t)||n(s)}},isTagEqual:function(e,t){return e.tagName===t},_xmlGenerator:null,xmlGenerator:function(){return F._xmlGenerator||(F._xmlGenerator=_()),F._xmlGenerator},xmlElement:function(e){if(!e)return null;for(var t=F.xmlGenerator().createElement(e),n=1;n<arguments.length;n++){var r=arguments[n];if(r)if("string"==typeof r||"number"==typeof r)t.appendChild(F.xmlTextNode(r));else if("object"===m(r)&&"function"==typeof r.sort)for(var s=0;s<r.length;s++){var i=r[s];"object"===m(i)&&"function"==typeof i.sort&&void 0!==i[1]&&null!==i[1]&&t.setAttribute(i[0],i[1])}else if("object"===m(r))for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&void 0!==r[o]&&null!==r[o]&&t.setAttribute(o,r[o])}return t},xmlescape:function(e){return e=(e=(e=(e=(e=e.replace(/\&/g,"&amp;")).replace(/</g,"&lt;")).replace(/>/g,"&gt;")).replace(/'/g,"&apos;")).replace(/"/g,"&quot;")},xmlunescape:function(e){return e=(e=(e=(e=(e=e.replace(/\&amp;/g,"&")).replace(/&lt;/g,"<")).replace(/&gt;/g,">")).replace(/&apos;/g,"'")).replace(/&quot;/g,'"')},xmlTextNode:function(e){return F.xmlGenerator().createTextNode(e)},xmlHtmlNode:function(e){var t;return d?t=(new d).parseFromString(e,"text/xml"):((t=new ActiveXObject("Microsoft.XMLDOM")).async="false",t.loadXML(e)),t},getText:function(e){if(!e)return null;var t="";0===e.childNodes.length&&e.nodeType===F.ElementType.TEXT&&(t+=e.nodeValue);for(var n=0;n<e.childNodes.length;n++)e.childNodes[n].nodeType===F.ElementType.TEXT&&(t+=e.childNodes[n].nodeValue);return F.xmlescape(t)},copyElement:function(e){var t;if(e.nodeType===F.ElementType.NORMAL){t=F.xmlElement(e.tagName);for(var n=0;n<e.attributes.length;n++)t.setAttribute(e.attributes[n].nodeName,e.attributes[n].value);for(var r=0;r<e.childNodes.length;r++)t.appendChild(F.copyElement(e.childNodes[r]))}else e.nodeType===F.ElementType.TEXT&&(t=F.xmlGenerator().createTextNode(e.nodeValue));return t},createHtml:function(e){var t;if(e.nodeType===F.ElementType.NORMAL){var n=e.nodeName.toLowerCase();if(F.XHTML.validTag(n))try{t=F.xmlElement(n);for(var r=0;r<F.XHTML.attributes[n].length;r++){var s=F.XHTML.attributes[n][r],i=e.getAttribute(s);if(null!=i&&""!==i&&!1!==i&&0!==i)if("style"===s&&"object"===m(i)&&void 0!==i.cssText&&(i=i.cssText),"style"===s){for(var o=[],a=i.split(";"),c=0;c<a.length;c++){var u,h=a[c].split(":"),l=h[0].replace(/^\s*/,"").replace(/\s*$/,"").toLowerCase();F.XHTML.validCSS(l)&&(u=h[1].replace(/^\s*/,"").replace(/\s*$/,""),o.push(l+": "+u))}0<o.length&&(i=o.join("; "),t.setAttribute(s,i))}else t.setAttribute(s,i)}for(var d=0;d<e.childNodes.length;d++)t.appendChild(F.createHtml(e.childNodes[d]))}catch(e){t=F.xmlTextNode("")}else{t=F.xmlGenerator().createDocumentFragment();for(var _=0;_<e.childNodes.length;_++)t.appendChild(F.createHtml(e.childNodes[_]))}}else if(e.nodeType===F.ElementType.FRAGMENT){t=F.xmlGenerator().createDocumentFragment();for(var f=0;f<e.childNodes.length;f++)t.appendChild(F.createHtml(e.childNodes[f]))}else e.nodeType===F.ElementType.TEXT&&(t=F.xmlTextNode(e.nodeValue));return t},escapeNode:function(e){return"string"!=typeof e?e:e.replace(/^\s+|\s+$/g,"").replace(/\\/g,"\\5c").replace(/ /g,"\\20").replace(/\"/g,"\\22").replace(/\&/g,"\\26").replace(/\'/g,"\\27").replace(/\//g,"\\2f").replace(/:/g,"\\3a").replace(/</g,"\\3c").replace(/>/g,"\\3e").replace(/@/g,"\\40")},unescapeNode:function(e){return"string"!=typeof e?e:e.replace(/\\20/g," ").replace(/\\22/g,'"').replace(/\\26/g,"&").replace(/\\27/g,"'").replace(/\\2f/g,"/").replace(/\\3a/g,":").replace(/\\3c/g,"<").replace(/\\3e/g,">").replace(/\\40/g,"@").replace(/\\5c/g,"\\")},getNodeFromJid:function(e){return e.indexOf("@")<0?null:e.split("@")[0]},getDomainFromJid:function(e){var t=F.getBareJidFromJid(e);if(t.indexOf("@")<0)return t;var n=t.split("@");return n.splice(0,1),n.join("@")},getResourceFromJid:function(e){if(!e)return null;var t=e.split("/");return t.length<2?null:(t.splice(0,1),t.join("/"))},getBareJidFromJid:function(e){return e?e.split("/")[0]:null},_handleError:function(e){void 0!==e.stack&&F.fatal(e.stack),e.sourceURL?F.fatal("error: "+this.handler+" "+e.sourceURL+":"+e.line+" - "+e.name+": "+e.message):e.fileName?F.fatal("error: "+this.handler+" "+e.fileName+":"+e.lineNumber+" - "+e.name+": "+e.message):F.fatal("error: "+e.message)},log:function(e,t){e===this.LogLevel.FATAL&&"object"===m(window.console)&&"function"==typeof window.console.error&&window.console.error(t)},debug:function(e){this.log(this.LogLevel.DEBUG,e)},info:function(e){this.log(this.LogLevel.INFO,e)},warn:function(e){this.log(this.LogLevel.WARN,e)},error:function(e){this.log(this.LogLevel.ERROR,e)},fatal:function(e){this.log(this.LogLevel.FATAL,e)},serialize:function(n){if(!n)return null;"function"==typeof n.tree&&(n=n.tree());var e=h(Array(n.attributes.length).keys()).map(function(e){return n.attributes[e].nodeName});e.sort();var t=e.reduce(function(e,t){return"".concat(e," ").concat(t,'="').concat(F.xmlescape(n.attributes.getNamedItem(t).value),'"')},"<".concat(n.nodeName));if(0<n.childNodes.length){t+=">";for(var r=0;r<n.childNodes.length;r++){var s=n.childNodes[r];switch(s.nodeType){case F.ElementType.NORMAL:t+=F.serialize(s);break;case F.ElementType.TEXT:t+=F.xmlescape(s.nodeValue);break;case F.ElementType.CDATA:t+="<![CDATA["+s.nodeValue+"]]>"}}t+="</"+n.nodeName+">"}else t+="/>";return t},_requestId:0,_connectionPlugins:{},addConnectionPlugin:function(e,t){F._connectionPlugins[e]=t}};F.Builder=function(){function n(e,t){a(this,n),"presence"!==e&&"message"!==e&&"iq"!==e||(t&&!t.xmlns?t.xmlns=F.NS.CLIENT:t=t||{xmlns:F.NS.CLIENT}),this.nodeTree=F.xmlElement(e,t),this.node=this.nodeTree}return i(n,[{key:"tree",value:function(){return this.nodeTree}},{key:"toString",value:function(){return F.serialize(this.nodeTree)}},{key:"up",value:function(){return this.node=this.node.parentNode,this}},{key:"root",value:function(){return this.node=this.nodeTree,this}},{key:"attrs",value:function(e){for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&(void 0===e[t]?this.node.removeAttribute(t):this.node.setAttribute(t,e[t]));return this}},{key:"c",value:function(e,t,n){var r=F.xmlElement(e,t,n);return this.node.appendChild(r),"string"!=typeof n&&"number"!=typeof n&&(this.node=r),this}},{key:"cnode",value:function(e){var t,n=F.xmlGenerator();try{t=void 0!==n.importNode}catch(e){t=!1}var r=t?n.importNode(e,!0):F.copyElement(e);return this.node.appendChild(r),this.node=r,this}},{key:"t",value:function(e){var t=F.xmlTextNode(e);return this.node.appendChild(t),this}},{key:"h",value:function(e){var t=F.xmlGenerator().createElement("body");t.innerHTML=e;for(var n=F.createHtml(t);0<n.childNodes.length;)this.node.appendChild(n.childNodes[0]);return this}}]),n}(),F.Handler=function(e,t,n,r,s,i,o){this.handler=e,this.ns=t,this.name=n,this.type=r,this.id=s,this.options=o||{matchBareFromJid:!1,ignoreNamespaceFragment:!1},this.options.matchBare&&(F.warn('The "matchBare" option is deprecated, use "matchBareFromJid" instead.'),this.options.matchBareFromJid=this.options.matchBare,delete this.options.matchBare),this.options.matchBareFromJid?this.from=i?F.getBareJidFromJid(i):null:this.from=i,this.user=!0},F.Handler.prototype={getNamespace:function(e){var t=e.getAttribute("xmlns");return t&&this.options.ignoreNamespaceFragment&&(t=t.split("#")[0]),t},namespaceMatch:function(e){var t=this,n=!1;return!this.ns||(F.forEachChild(e,null,function(e){t.getNamespace(e)===t.ns&&(n=!0)}),n||this.getNamespace(e)===this.ns)},isMatch:function(e){var t=e.getAttribute("from");this.options.matchBareFromJid&&(t=F.getBareJidFromJid(t));var n=e.getAttribute("type");return!(!this.namespaceMatch(e)||this.name&&!F.isTagEqual(e,this.name)||this.type&&(Array.isArray(this.type)?-1===this.type.indexOf(n):n!==this.type)||this.id&&e.getAttribute("id")!==this.id||this.from&&t!==this.from)},run:function(e){var t=null;try{t=this.handler(e)}catch(e){throw F._handleError(e),e}return t},toString:function(){return"{Handler: "+this.handler+"("+this.name+","+this.id+","+this.ns+")}"}},F.TimedHandler=function(){function n(e,t){a(this,n),this.period=e,this.handler=t,this.lastCalled=(new Date).getTime(),this.user=!0}return i(n,[{key:"run",value:function(){return this.lastCalled=(new Date).getTime(),this.handler()}},{key:"reset",value:function(){this.lastCalled=(new Date).getTime()}},{key:"toString",value:function(){return"{TimedHandler: "+this.handler+"("+this.period+")}"}}]),n}(),F.Connection=function(){function o(e,t){var n=this;a(this,o),this.service=e,this.options=t||{};var r,s=this.options.protocol||"";for(var i in this.options.worker?this._proto=new F.WorkerWebsocket(this):0===e.indexOf("ws:")||0===e.indexOf("wss:")||0===s.indexOf("ws")?this._proto=new F.Websocket(this):this._proto=new F.Bosh(this),this.jid="",this.domain=null,this.features=null,this._sasl_data={},this.do_bind=!1,this.do_session=!1,this.mechanisms={},this.timedHandlers=[],this.handlers=[],this.removeTimeds=[],this.removeHandlers=[],this.addTimeds=[],this.addHandlers=[],this.protocolErrorHandlers={HTTP:{},websocket:{}},this._idleTimeout=null,this._disconnectTimeout=null,this.authenticated=!1,this.connected=!1,this.disconnecting=!1,this.do_authentication=!0,this.paused=!1,this.restored=!1,this._data=[],this._uniqueId=0,this._sasl_success_handler=null,this._sasl_failure_handler=null,this._sasl_challenge_handler=null,this.maxRetries=5,this._idleTimeout=setTimeout(function(){return n._onIdle()},100),R.addCookies(this.options.cookies),this.registerSASLMechanisms(this.options.mechanisms),F._connectionPlugins){Object.prototype.hasOwnProperty.call(F._connectionPlugins,i)&&((r=function(){}).prototype=F._connectionPlugins[i],this[i]=new r,this[i].init(this))}}return i(o,[{key:"reset",value:function(){this._proto._reset(),this.do_session=!1,this.do_bind=!1,this.timedHandlers=[],this.handlers=[],this.removeTimeds=[],this.removeHandlers=[],this.addTimeds=[],this.addHandlers=[],this.authenticated=!1,this.connected=!1,this.disconnecting=!1,this.restored=!1,this._data=[],this._requests=[],this._uniqueId=0}},{key:"pause",value:function(){this.paused=!0}},{key:"resume",value:function(){this.paused=!1}},{key:"getUniqueId",value:function(e){var t="xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(e){var t=16*Math.random()|0;return("x"===e?t:3&t|8).toString(16)});return"string"==typeof e||"number"==typeof e?t+":"+e:t+""}},{key:"addProtocolErrorHandler",value:function(e,t,n){this.protocolErrorHandlers[e][t]=n}},{key:"connect",value:function(e,t,n,r,s,i,o){this.jid=e,this.authzid=F.getBareJidFromJid(this.jid),this.authcid=o||F.getNodeFromJid(this.jid),this.pass=t,this.connect_callback=n,this.disconnecting=!1,this.connected=!1,this.authenticated=!1,this.restored=!1,this.domain=F.getDomainFromJid(this.jid),this._changeConnectStatus(F.Status.CONNECTING,null),this._proto._connect(r,s,i)}},{key:"attach",value:function(e,t,n,r,s,i,o){if(this._proto._attach)return this._proto._attach(e,t,n,r,s,i,o);var a=new Error('The "attach" method is not available for your connection protocol');throw a.name="StropheSessionError",a}},{key:"restore",value:function(e,t,n,r,s){if(!this._sessionCachingSupported()){var i=new Error('The "restore" method can only be used with a BOSH connection.');throw i.name="StropheSessionError",i}this._proto._restore(e,t,n,r,s)}},{key:"_sessionCachingSupported",value:function(){if(this._proto instanceof F.Bosh){if(!JSON)return!1;try{sessionStorage.setItem("_strophe_","_strophe_"),sessionStorage.removeItem("_strophe_")}catch(e){return!1}return!0}return!1}},{key:"xmlInput",value:function(){}},{key:"xmlOutput",value:function(){}},{key:"rawInput",value:function(){}},{key:"rawOutput",value:function(){}},{key:"nextValidRid",value:function(){}},{key:"send",value:function(e){if(null!==e){if("function"==typeof e.sort)for(var t=0;t<e.length;t++)this._queueData(e[t]);else"function"==typeof e.tree?this._queueData(e.tree()):this._queueData(e);this._proto._send()}}},{key:"flush",value:function(){clearTimeout(this._idleTimeout),this._onIdle()}},{key:"sendPresence",value:function(e,t,n,r){var s=this,i=null;"function"==typeof e.tree&&(e=e.tree());var o,a=e.getAttribute("id");return a||(a=this.getUniqueId("sendPresence"),e.setAttribute("id",a)),"function"!=typeof t&&"function"!=typeof n||(o=this.addHandler(function(e){i&&s.deleteTimedHandler(i),"error"===e.getAttribute("type")?n&&n(e):t&&t(e)},null,"presence",null,a),r&&(i=this.addTimedHandler(r,function(){return s.deleteHandler(o),n&&n(null),!1}))),this.send(e),a}},{key:"sendIQ",value:function(e,r,s,t){var i=this,o=null;"function"==typeof e.tree&&(e=e.tree());var n,a=e.getAttribute("id");return a||(a=this.getUniqueId("sendIQ"),e.setAttribute("id",a)),"function"!=typeof r&&"function"!=typeof s||(n=this.addHandler(function(e){o&&i.deleteTimedHandler(o);var t=e.getAttribute("type");if("result"===t)r&&r(e);else{if("error"!==t){var n=new Error("Got bad IQ type of ".concat(t));throw n.name="StropheError",n}s&&s(e)}},null,"iq",["error","result"],a),t&&(o=this.addTimedHandler(t,function(){return i.deleteHandler(n),s&&s(null),!1}))),this.send(e),a}},{key:"_queueData",value:function(e){if(null===e||!e.tagName||!e.childNodes){var t=new Error("Cannot queue non-DOMElement.");throw t.name="StropheError",t}this._data.push(e)}},{key:"_sendRestart",value:function(){var e=this;this._data.push("restart"),this._proto._sendRestart(),this._idleTimeout=setTimeout(function(){return e._onIdle()},100)}},{key:"addTimedHandler",value:function(e,t){var n=new F.TimedHandler(e,t);return this.addTimeds.push(n),n}},{key:"deleteTimedHandler",value:function(e){this.removeTimeds.push(e)}},{key:"addHandler",value:function(e,t,n,r,s,i,o){var a=new F.Handler(e,t,n,r,s,i,o);return this.addHandlers.push(a),a}},{key:"deleteHandler",value:function(e){this.removeHandlers.push(e);var t=this.addHandlers.indexOf(e);0<=t&&this.addHandlers.splice(t,1)}},{key:"registerSASLMechanisms",value:function(e){var t=this;this.mechanisms={},(e=e||[F.SASLAnonymous,F.SASLExternal,F.SASLOAuthBearer,F.SASLXOAuth2,F.SASLPlain,F.SASLSHA1]).forEach(function(e){return t.registerSASLMechanism(e)})}},{key:"registerSASLMechanism",value:function(e){var t=new e;this.mechanisms[t.mechname]=t}},{key:"disconnect",value:function(e){var t;this._changeConnectStatus(F.Status.DISCONNECTING,e),e?F.warn("Disconnect was called because: "+e):F.info("Disconnect was called"),this.connected?(t=!1,this.disconnecting=!0,this.authenticated&&(t=D({xmlns:F.NS.CLIENT,type:"unavailable"})),this._disconnectTimeout=this._addSysTimedHandler(3e3,this._onDisconnectTimeout.bind(this)),this._proto._disconnect(t)):(F.warn("Disconnect was called before Strophe connected to the server"),this._proto._abortAllRequests(),this._doDisconnect())}},{key:"_changeConnectStatus",value:function(e,t,n){for(var r in F._connectionPlugins)if(Object.prototype.hasOwnProperty.call(F._connectionPlugins,r)){var s=this[r];if(s.statusChanged)try{s.statusChanged(e,t)}catch(e){F.error("".concat(r," plugin caused an exception changing status: ").concat(e))}}if(this.connect_callback)try{this.connect_callback(e,t,n)}catch(e){F._handleError(e),F.error("User connection callback caused an exception: ".concat(e))}}},{key:"_doDisconnect",value:function(e){"number"==typeof this._idleTimeout&&clearTimeout(this._idleTimeout),null!==this._disconnectTimeout&&(this.deleteTimedHandler(this._disconnectTimeout),this._disconnectTimeout=null),F.debug("_doDisconnect was called"),this._proto._doDisconnect(),this.authenticated=!1,this.disconnecting=!1,this.restored=!1,this.handlers=[],this.timedHandlers=[],this.removeTimeds=[],this.removeHandlers=[],this.addTimeds=[],this.addHandlers=[],this._changeConnectStatus(F.Status.DISCONNECTED,e),this.connected=!1}},{key:"_dataRecv",value:function(e,t){var s=this,n=this._proto._reqToData(e);if(null!==n){for(this.xmlInput!==F.Connection.prototype.xmlInput&&(n.nodeName===this._proto.strip&&n.childNodes.length?this.xmlInput(n.childNodes[0]):this.xmlInput(n)),this.rawInput!==F.Connection.prototype.rawInput&&(t?this.rawInput(t):this.rawInput(F.serialize(n)));0<this.removeHandlers.length;){var r=this.removeHandlers.pop(),i=this.handlers.indexOf(r);0<=i&&this.handlers.splice(i,1)}for(;0<this.addHandlers.length;)this.handlers.push(this.addHandlers.pop());if(this.disconnecting&&this._proto._emptyQueue())this._doDisconnect();else{var o=n.getAttribute("type");if(null!==o&&"terminate"===o){if(this.disconnecting)return;var a=n.getAttribute("condition"),c=n.getElementsByTagName("conflict");return null!==a?("remote-stream-error"===a&&0<c.length&&(a="conflict"),this._changeConnectStatus(F.Status.CONNFAIL,a)):this._changeConnectStatus(F.Status.CONNFAIL,F.ErrorCondition.UNKOWN_REASON),void this._doDisconnect(a)}F.forEachChild(n,null,function(e){var t=s.handlers;s.handlers=[];for(var n=0;n<t.length;n++){var r=t[n];try{(!r.isMatch(e)||!s.authenticated&&r.user||r.run(e))&&s.handlers.push(r)}catch(e){F.warn("Removing Strophe handlers due to uncaught exception: "+e.message)}}})}}}},{key:"_connect_cb",value:function(e,t,n){var r,s,i=this;F.debug("_connect_cb was called"),this.connected=!0;try{r=this._proto._reqToData(e)}catch(e){if(e.name!==F.ErrorCondition.BAD_FORMAT)throw e;this._changeConnectStatus(F.Status.CONNFAIL,F.ErrorCondition.BAD_FORMAT),this._doDisconnect(F.ErrorCondition.BAD_FORMAT)}r&&(this.xmlInput!==F.Connection.prototype.xmlInput&&(r.nodeName===this._proto.strip&&r.childNodes.length?this.xmlInput(r.childNodes[0]):this.xmlInput(r)),this.rawInput!==F.Connection.prototype.rawInput&&(n?this.rawInput(n):this.rawInput(F.serialize(r))),this._proto._connect_cb(r)!==F.Status.CONNFAIL&&((r.getElementsByTagNameNS?0<r.getElementsByTagNameNS(F.NS.STREAM,"features").length:0<r.getElementsByTagName("stream:features").length||0<r.getElementsByTagName("features").length)&&(0!==(s=Array.from(r.getElementsByTagName("mechanism")).map(function(e){return i.mechanisms[e.textContent]}).filter(function(e){return e})).length||0!==r.getElementsByTagName("auth").length)?!1!==this.do_authentication&&this.authenticate(s):this._proto._no_auth_received(t)))}},{key:"sortMechanismsByPriority",value:function(e){for(var t=0;t<e.length-1;++t){for(var n,r=t,s=t+1;s<e.length;++s)e[s].priority>e[r].priority&&(r=s);r!==t&&(n=e[t],e[t]=e[r],e[r]=n)}return e}},{key:"authenticate",value:function(e){this._attemptSASLAuth(e)||this._attemptLegacyAuth()}},{key:"_attemptSASLAuth",value:function(e){e=this.sortMechanismsByPriority(e||[]);for(var t=!1,n=0;n<e.length;++n)if(e[n].test(this)){this._sasl_success_handler=this._addSysHandler(this._sasl_success_cb.bind(this),null,"success",null,null),this._sasl_failure_handler=this._addSysHandler(this._sasl_failure_cb.bind(this),null,"failure",null,null),this._sasl_challenge_handler=this._addSysHandler(this._sasl_challenge_cb.bind(this),null,"challenge",null,null),this._sasl_mechanism=e[n],this._sasl_mechanism.onStart(this);var r,s=M("auth",{xmlns:F.NS.SASL,mechanism:this._sasl_mechanism.mechname});this._sasl_mechanism.isClientFirst&&(r=this._sasl_mechanism.onChallenge(this,null),s.t(H.btoa(r))),this.send(s.tree()),t=!0;break}return t}},{key:"_sasl_challenge_cb",value:function(e){var t=H.atob(F.getText(e)),n=this._sasl_mechanism.onChallenge(this,t),r=M("response",{xmlns:F.NS.SASL});return""!==n&&r.t(H.btoa(n)),this.send(r.tree()),!0}},{key:"_attemptLegacyAuth",value:function(){null===F.getNodeFromJid(this.jid)?(this._changeConnectStatus(F.Status.CONNFAIL,F.ErrorCondition.MISSING_JID_NODE),this.disconnect(F.ErrorCondition.MISSING_JID_NODE)):(this._changeConnectStatus(F.Status.AUTHENTICATING,null),this._addSysHandler(this._onLegacyAuthIQResult.bind(this),null,null,null,"_auth_1"),this.send(q({type:"get",to:this.domain,id:"_auth_1"}).c("query",{xmlns:F.NS.AUTH}).c("username",{}).t(F.getNodeFromJid(this.jid)).tree()))}},{key:"_onLegacyAuthIQResult",value:function(){var e=q({type:"set",id:"_auth_2"}).c("query",{xmlns:F.NS.AUTH}).c("username",{}).t(F.getNodeFromJid(this.jid)).up().c("password").t(this.pass);return F.getResourceFromJid(this.jid)||(this.jid=F.getBareJidFromJid(this.jid)+"/strophe"),e.up().c("resource",{}).t(F.getResourceFromJid(this.jid)),this._addSysHandler(this._auth2_cb.bind(this),null,null,null,"_auth_2"),this.send(e.tree()),!1}},{key:"_sasl_success_cb",value:function(e){var n=this;if(this._sasl_data["server-signature"]){var t,r=H.atob(F.getText(e)).match(/([a-z]+)=([^,]+)(,|$)/);if("v"===r[1]&&(t=r[2]),t!==this._sasl_data["server-signature"])return this.deleteHandler(this._sasl_failure_handler),this._sasl_failure_handler=null,this._sasl_challenge_handler&&(this.deleteHandler(this._sasl_challenge_handler),this._sasl_challenge_handler=null),this._sasl_data={},this._sasl_failure_cb(null)}F.info("SASL authentication succeeded."),this._sasl_mechanism&&this._sasl_mechanism.onSuccess(),this.deleteHandler(this._sasl_failure_handler),this._sasl_failure_handler=null,this._sasl_challenge_handler&&(this.deleteHandler(this._sasl_challenge_handler),this._sasl_challenge_handler=null);function s(e,t){for(;e.length;)n.deleteHandler(e.pop());return n._onStreamFeaturesAfterSASL(t),!1}var i=[];return i.push(this._addSysHandler(function(e){return s(i,e)},null,"stream:features",null,null)),i.push(this._addSysHandler(function(e){return s(i,e)},F.NS.STREAM,"features",null,null)),this._sendRestart(),!1}},{key:"_onStreamFeaturesAfterSASL",value:function(e){this.features=e;for(var t=0;t<e.childNodes.length;t++){var n=e.childNodes[t];"bind"===n.nodeName&&(this.do_bind=!0),"session"===n.nodeName&&(this.do_session=!0)}return this.do_bind?this.options.explicitResourceBinding?this._changeConnectStatus(F.Status.BINDREQUIRED,null):this.bind():this._changeConnectStatus(F.Status.AUTHFAIL,null),!1}},{key:"bind",value:function(){var e;this.do_bind?(this._addSysHandler(this._onResourceBindResultIQ.bind(this),null,null,null,"_bind_auth_2"),(e=F.getResourceFromJid(this.jid))?this.send(q({type:"set",id:"_bind_auth_2"}).c("bind",{xmlns:F.NS.BIND}).c("resource",{}).t(e).tree()):this.send(q({type:"set",id:"_bind_auth_2"}).c("bind",{xmlns:F.NS.BIND}).tree())):F.log(F.LogLevel.INFO,'Strophe.Connection.prototype.bind called but "do_bind" is false')}},{key:"_onResourceBindResultIQ",value:function(e){var t;if("error"===e.getAttribute("type"))return F.warn("Resource binding failed."),0<e.getElementsByTagName("conflict").length&&(t=F.ErrorCondition.CONFLICT),this._changeConnectStatus(F.Status.AUTHFAIL,t,e),!1;var n=e.getElementsByTagName("bind");if(!(0<n.length))return F.warn("Resource binding failed."),this._changeConnectStatus(F.Status.AUTHFAIL,null,e),!1;var r=n[0].getElementsByTagName("jid");0<r.length&&(this.authenticated=!0,this.jid=F.getText(r[0]),this.do_session?this._establishSession():this._changeConnectStatus(F.Status.CONNECTED,null))}},{key:"_establishSession",value:function(){if(!this.do_session)throw new Error("Strophe.Connection.prototype._establishSession "+"called but apparently ".concat(F.NS.SESSION," wasn't advertised by the server"));this._addSysHandler(this._onSessionResultIQ.bind(this),null,null,null,"_session_auth_2"),this.send(q({type:"set",id:"_session_auth_2"}).c("session",{xmlns:F.NS.SESSION}).tree())}},{key:"_onSessionResultIQ",value:function(e){if("result"===e.getAttribute("type"))this.authenticated=!0,this._changeConnectStatus(F.Status.CONNECTED,null);else if("error"===e.getAttribute("type"))return this.authenticated=!1,F.warn("Session creation failed."),this._changeConnectStatus(F.Status.AUTHFAIL,null,e),!1;return!1}},{key:"_sasl_failure_cb",value:function(e){return this._sasl_success_handler&&(this.deleteHandler(this._sasl_success_handler),this._sasl_success_handler=null),this._sasl_challenge_handler&&(this.deleteHandler(this._sasl_challenge_handler),this._sasl_challenge_handler=null),this._sasl_mechanism&&this._sasl_mechanism.onFailure(),this._changeConnectStatus(F.Status.AUTHFAIL,null,e),!1}},{key:"_auth2_cb",value:function(e){return"result"===e.getAttribute("type")?(this.authenticated=!0,this._changeConnectStatus(F.Status.CONNECTED,null)):"error"===e.getAttribute("type")&&(this._changeConnectStatus(F.Status.AUTHFAIL,null,e),this.disconnect("authentication failed")),!1}},{key:"_addSysTimedHandler",value:function(e,t){var n=new F.TimedHandler(e,t);return n.user=!1,this.addTimeds.push(n),n}},{key:"_addSysHandler",value:function(e,t,n,r,s){var i=new F.Handler(e,t,n,r,s);return i.user=!1,this.addHandlers.push(i),i}},{key:"_onDisconnectTimeout",value:function(){return F.debug("_onDisconnectTimeout was called"),this._changeConnectStatus(F.Status.CONNTIMEOUT,null),this._proto._onDisconnectTimeout(),this._doDisconnect(),!1}},{key:"_onIdle",value:function(){for(var e=this;0<this.addTimeds.length;)this.timedHandlers.push(this.addTimeds.pop());for(;0<this.removeTimeds.length;){var t=this.removeTimeds.pop(),n=this.timedHandlers.indexOf(t);0<=n&&this.timedHandlers.splice(n,1)}for(var r=(new Date).getTime(),s=[],i=0;i<this.timedHandlers.length;i++){var o=this.timedHandlers[i];!this.authenticated&&o.user||o.lastCalled+o.period-r<=0&&!o.run()||s.push(o)}this.timedHandlers=s,clearTimeout(this._idleTimeout),this._proto._onIdle(),this.connected&&(this._idleTimeout=setTimeout(function(){return e._onIdle()},100))}}]),o}(),F.SASLMechanism=function(){function r(e,t,n){a(this,r),this.mechname=e,this.isClientFirst=t,this.priority=n}return i(r,[{key:"test",value:function(){return!0}},{key:"onStart",value:function(e){this._connection=e}},{key:"onChallenge",value:function(){throw new Error("You should implement challenge handling!")}},{key:"onFailure",value:function(){this._connection=null}},{key:"onSuccess",value:function(){this._connection=null}}]),r}(),F.SASLAnonymous=function(){o(s,F.SASLMechanism);var r=u(s);function s(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"ANONYMOUS",t=1<arguments.length&&void 0!==arguments[1]&&arguments[1],n=2<arguments.length&&void 0!==arguments[2]?arguments[2]:20;return a(this,s),r.call(this,e,t,n)}return i(s,[{key:"test",value:function(e){return null===e.authcid}}]),s}(),F.SASLPlain=function(){o(s,F.SASLMechanism);var r=u(s);function s(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"PLAIN",t=!(1<arguments.length&&void 0!==arguments[1])||arguments[1],n=2<arguments.length&&void 0!==arguments[2]?arguments[2]:50;return a(this,s),r.call(this,e,t,n)}return i(s,[{key:"test",value:function(e){return null!==e.authcid}},{key:"onChallenge",value:function(e){var t=e.authcid,n=e.authzid,r=e.domain,s=e.pass;if(!r)throw new Error("SASLPlain onChallenge: domain is not defined!");var i=n!=="".concat(t,"@").concat(r)?n:"";return i+="\0",i+=t,i+="\0",i+=s,R.utf16to8(i)}}]),s}(),F.SASLSHA1=function(){o(s,F.SASLMechanism);var r=u(s);function s(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"SCRAM-SHA-1",t=!(1<arguments.length&&void 0!==arguments[1])||arguments[1],n=2<arguments.length&&void 0!==arguments[2]?arguments[2]:60;return a(this,s),r.call(this,e,t,n)}return i(s,[{key:"test",value:function(e){return null!==e.authcid}},{key:"onChallenge",value:function(e,t,n){var r=n||x.hexdigest(""+1234567890*Math.random()),s="n="+R.utf16to8(e.authcid);return s+=",r=",s+=r,e._sasl_data.cnonce=r,s="n,,"+(e._sasl_data["client-first-message-bare"]=s),this.onChallenge=function(e,t){for(var n,r,s,i,o,a,c="c=biws,",u="".concat(e._sasl_data["client-first-message-bare"],",").concat(t,","),h=e._sasl_data.cnonce,l=/([a-z]+)=([^,]+)(,|$)/;t.match(l);){var d=t.match(l);switch(t=t.replace(d[0],""),d[1]){case"r":n=d[2];break;case"s":r=d[2];break;case"i":s=d[2]}}if(n.substr(0,h.length)!==h)return e._sasl_data={},e._sasl_failure_cb();u+=c+="r="+n,r=H.atob(r),r+="\0\0\0";for(var _=R.utf16to8(e.pass),f=o=I.core_hmac_sha1(_,r),m=1;m<s;m++){for(i=I.core_hmac_sha1(_,I.binb2str(o)),a=0;a<5;a++)f[a]^=i[a];o=i}f=I.binb2str(f);var p=I.core_hmac_sha1(f,"Client Key"),g=I.str_hmac_sha1(f,"Server Key"),v=I.core_hmac_sha1(I.str_sha1(I.binb2str(p)),u);for(e._sasl_data["server-signature"]=I.b64_hmac_sha1(g,u),a=0;a<5;a++)p[a]^=v[a];return c+=",p="+H.btoa(I.binb2str(p))},s}}]),s}(),F.SASLOAuthBearer=function(){o(s,F.SASLMechanism);var r=u(s);function s(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"OAUTHBEARER",t=!(1<arguments.length&&void 0!==arguments[1])||arguments[1],n=2<arguments.length&&void 0!==arguments[2]?arguments[2]:40;return a(this,s),r.call(this,e,t,n)}return i(s,[{key:"test",value:function(e){return null!==e.pass}},{key:"onChallenge",value:function(e){var t="n,";return null!==e.authcid&&(t=t+"a="+e.authzid),t+=",",t+="",t+="auth=Bearer ",t+=e.pass,t+="",t+="",R.utf16to8(t)}}]),s}(),F.SASLExternal=function(){o(s,F.SASLMechanism);var r=u(s);function s(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"EXTERNAL",t=!(1<arguments.length&&void 0!==arguments[1])||arguments[1],n=2<arguments.length&&void 0!==arguments[2]?arguments[2]:10;return a(this,s),r.call(this,e,t,n)}return i(s,[{key:"onChallenge",value:function(e){return e.authcid===e.authzid?"":e.authzid}}]),s}(),F.SASLXOAuth2=function(){o(s,F.SASLMechanism);var r=u(s);function s(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"X-OAUTH2",t=!(1<arguments.length&&void 0!==arguments[1])||arguments[1],n=2<arguments.length&&void 0!==arguments[2]?arguments[2]:30;return a(this,s),r.call(this,e,t,n)}return i(s,[{key:"test",value:function(e){return null!==e.pass}},{key:"onChallenge",value:function(e){var t="\0";return null!==e.authcid&&(t+=e.authzid),t+="\0",t+=e.pass,R.utf16to8(t)}}]),s}();var j={Strophe:F,$build:M,$iq:q,$msg:L,$pres:D,SHA1:I,MD5:x,b64_hmac_sha1:I.b64_hmac_sha1,b64_sha1:I.b64_sha1,str_hmac_sha1:I.str_hmac_sha1,str_sha1:I.str_sha1};F.Request=function(){function s(e,t,n,r){a(this,s),this.id=++F._requestId,this.xmlData=e,this.data=F.serialize(e),this.origFunc=t,this.func=t,this.rid=n,this.date=NaN,this.sends=r||0,this.abort=!1,this.dead=null,this.age=function(){return this.date?(new Date-this.date)/1e3:0},this.timeDead=function(){return this.dead?(new Date-this.dead)/1e3:0},this.xhr=this._newXHR()}return i(s,[{key:"getResponse",value:function(){var e=null;if(this.xhr.responseXML&&this.xhr.responseXML.documentElement){if("parsererror"===(e=this.xhr.responseXML.documentElement).tagName)throw F.error("invalid response received"),F.error("responseText: "+this.xhr.responseText),F.error("responseXML: "+F.serialize(this.xhr.responseXML)),new Error("parsererror")}else if(this.xhr.responseText){if(F.debug("Got responseText but no responseXML; attempting to parse it with DOMParser..."),!(e=(new d).parseFromString(this.xhr.responseText,"application/xml").documentElement))throw new Error("Parsing produced null node");if(e.querySelector("parsererror")){F.error("invalid response received: "+e.querySelector("parsererror").textContent),F.error("responseText: "+this.xhr.responseText);var t=new Error;throw t.name=F.ErrorCondition.BAD_FORMAT,t}}return e}},{key:"_newXHR",value:function(){var e=null;return window.XMLHttpRequest?(e=new XMLHttpRequest).overrideMimeType&&e.overrideMimeType("text/xml; charset=utf-8"):window.ActiveXObject&&(e=new ActiveXObject("Microsoft.XMLHTTP")),e.onreadystatechange=this.func.bind(null,this),e}}]),s}(),F.Bosh=function(){function l(e){a(this,l),this._conn=e,this.rid=Math.floor(4294967295*Math.random()),this.sid=null,this.hold=1,this.wait=60,this.window=5,this.errors=0,this.inactivity=null,this.lastResponseHeaders=null,this._requests=[]}return i(l,[{key:"_buildBody",value:function(){var e=M("body",{rid:this.rid++,xmlns:F.NS.HTTPBIND});return null!==this.sid&&e.attrs({sid:this.sid}),this._conn.options.keepalive&&this._conn._sessionCachingSupported()&&this._cacheSession(),e}},{key:"_reset",value:function(){this.rid=Math.floor(4294967295*Math.random()),this.sid=null,this.errors=0,this._conn._sessionCachingSupported()&&window.sessionStorage.removeItem("strophe-bosh-session"),this._conn.nextValidRid(this.rid)}},{key:"_connect",value:function(e,t,n){this.wait=e||this.wait,this.hold=t||this.hold,this.errors=0;var r=this._buildBody().attrs({to:this._conn.domain,"xml:lang":"en",wait:this.wait,hold:this.hold,content:"text/xml; charset=utf-8",ver:"1.6","xmpp:version":"1.0","xmlns:xmpp":F.NS.BOSH});n&&r.attrs({route:n});var s=this._conn._connect_cb;this._requests.push(new F.Request(r.tree(),this._onRequestStateChange.bind(this,s.bind(this._conn)),r.tree().getAttribute("rid"))),this._throttledRequestHandler()}},{key:"_attach",value:function(e,t,n,r,s,i,o){this._conn.jid=e,this.sid=t,this.rid=n,this._conn.connect_callback=r,this._conn.domain=F.getDomainFromJid(this._conn.jid),this._conn.authenticated=!0,this._conn.connected=!0,this.wait=s||this.wait,this.hold=i||this.hold,this.window=o||this.window,this._conn._changeConnectStatus(F.Status.ATTACHED,null)}},{key:"_restore",value:function(e,t,n,r,s){var i=JSON.parse(window.sessionStorage.getItem("strophe-bosh-session"));if(!(null!=i&&i.rid&&i.sid&&i.jid&&(null==e||F.getBareJidFromJid(i.jid)===F.getBareJidFromJid(e)||null===F.getNodeFromJid(e)&&F.getDomainFromJid(i.jid)===e))){var o=new Error("_restore: no restoreable session.");throw o.name="StropheSessionError",o}this._conn.restored=!0,this._attach(i.jid,i.sid,i.rid,t,n,r,s)}},{key:"_cacheSession",value:function(){this._conn.authenticated?this._conn.jid&&this.rid&&this.sid&&window.sessionStorage.setItem("strophe-bosh-session",JSON.stringify({jid:this._conn.jid,rid:this.rid,sid:this.sid})):window.sessionStorage.removeItem("strophe-bosh-session")}},{key:"_connect_cb",value:function(e){var t=e.getAttribute("type");if(null!==t&&"terminate"===t){var n=e.getAttribute("condition");F.error("BOSH-Connection failed: "+n);var r=e.getElementsByTagName("conflict");return null!==n?("remote-stream-error"===n&&0<r.length&&(n="conflict"),this._conn._changeConnectStatus(F.Status.CONNFAIL,n)):this._conn._changeConnectStatus(F.Status.CONNFAIL,"unknown"),this._conn._doDisconnect(n),F.Status.CONNFAIL}this.sid||(this.sid=e.getAttribute("sid"));var s=e.getAttribute("requests");s&&(this.window=parseInt(s,10));var i=e.getAttribute("hold");i&&(this.hold=parseInt(i,10));var o=e.getAttribute("wait");o&&(this.wait=parseInt(o,10));var a=e.getAttribute("inactivity");a&&(this.inactivity=parseInt(a,10))}},{key:"_disconnect",value:function(e){this._sendTerminate(e)}},{key:"_doDisconnect",value:function(){this.sid=null,this.rid=Math.floor(4294967295*Math.random()),this._conn._sessionCachingSupported()&&window.sessionStorage.removeItem("strophe-bosh-session"),this._conn.nextValidRid(this.rid)}},{key:"_emptyQueue",value:function(){return 0===this._requests.length}},{key:"_callProtocolErrorHandlers",value:function(e){var t=l._getRequestStatus(e),n=this._conn.protocolErrorHandlers.HTTP[t];n&&n.call(this,t)}},{key:"_hitError",value:function(e){this.errors++,F.warn("request errored, status: "+e+", number of errors: "+this.errors),4<this.errors&&this._conn._onDisconnectTimeout()}},{key:"_no_auth_received",value:function(e){F.warn("Server did not yet offer a supported authentication mechanism. Sending a blank poll request."),e=e?e.bind(this._conn):this._conn._connect_cb.bind(this._conn);var t=this._buildBody();this._requests.push(new F.Request(t.tree(),this._onRequestStateChange.bind(this,e),t.tree().getAttribute("rid"))),this._throttledRequestHandler()}},{key:"_onDisconnectTimeout",value:function(){this._abortAllRequests()}},{key:"_abortAllRequests",value:function(){for(;0<this._requests.length;){var e=this._requests.pop();e.abort=!0,e.xhr.abort(),e.xhr.onreadystatechange=function(){}}}},{key:"_onIdle",value:function(){var e,t=this._conn._data;if(this._conn.authenticated&&0===this._requests.length&&0===t.length&&!this._conn.disconnecting&&(F.debug("no requests during idle cycle, sending blank request"),t.push(null)),!this._conn.paused){if(this._requests.length<2&&0<t.length){for(var n=this._buildBody(),r=0;r<t.length;r++)null!==t[r]&&("restart"===t[r]?n.attrs({to:this._conn.domain,"xml:lang":"en","xmpp:restart":"true","xmlns:xmpp":F.NS.BOSH}):n.cnode(t[r]).up());delete this._conn._data,this._conn._data=[],this._requests.push(new F.Request(n.tree(),this._onRequestStateChange.bind(this,this._conn._dataRecv.bind(this._conn)),n.tree().getAttribute("rid"))),this._throttledRequestHandler()}0<this._requests.length&&(e=this._requests[0].age(),null!==this._requests[0].dead&&this._requests[0].timeDead()>Math.floor(F.SECONDARY_TIMEOUT*this.wait)&&this._throttledRequestHandler(),e>Math.floor(F.TIMEOUT*this.wait)&&(F.warn("Request "+this._requests[0].id+" timed out, over "+Math.floor(F.TIMEOUT*this.wait)+" seconds since last activity"),this._throttledRequestHandler()))}}},{key:"_onRequestStateChange",value:function(e,t){if(F.debug("request id "+t.id+"."+t.sends+" state changed to "+t.xhr.readyState),t.abort)t.abort=!1;else if(4===t.xhr.readyState){var n=l._getRequestStatus(t);if(this.lastResponseHeaders=t.xhr.getAllResponseHeaders(),this._conn.disconnecting&&400<=n)return this._hitError(n),void this._callProtocolErrorHandlers(t);var r,s=0<n&&n<500,i=t.sends>this._conn.maxRetries;(s||i)&&(this._removeRequest(t),F.debug("request id "+t.id+" should now be removed")),200===n?(r=this._requests[0]===t,(this._requests[1]===t||r&&0<this._requests.length&&this._requests[0].age()>Math.floor(F.SECONDARY_TIMEOUT*this.wait))&&this._restartRequest(0),this._conn.nextValidRid(Number(t.rid)+1),F.debug("request id "+t.id+"."+t.sends+" got 200"),e(t),this.errors=0):0===n||400<=n&&n<600||12e3<=n?(F.error("request id "+t.id+"."+t.sends+" error "+n+" happened"),this._hitError(n),this._callProtocolErrorHandlers(t),400<=n&&n<500&&(this._conn._changeConnectStatus(F.Status.DISCONNECTING,null),this._conn._doDisconnect())):F.error("request id "+t.id+"."+t.sends+" error "+n+" happened"),s||i?i&&!this._conn.connected&&this._conn._changeConnectStatus(F.Status.CONNFAIL,"giving-up"):this._throttledRequestHandler()}}},{key:"_processRequest",value:function(e){var n=this,r=this._requests[e],t=l._getRequestStatus(r,-1);if(r.sends>this._conn.maxRetries)this._conn._onDisconnectTimeout();else{var s=r.age(),i=!isNaN(s)&&s>Math.floor(F.TIMEOUT*this.wait),o=null!==r.dead&&r.timeDead()>Math.floor(F.SECONDARY_TIMEOUT*this.wait),a=4===r.xhr.readyState&&(t<1||500<=t);if((i||o||a)&&(o&&F.error("Request ".concat(this._requests[e].id," timed out (secondary), restarting")),r.abort=!0,r.xhr.abort(),r.xhr.onreadystatechange=function(){},this._requests[e]=new F.Request(r.xmlData,r.origFunc,r.rid,r.sends),r=this._requests[e]),0===r.xhr.readyState){F.debug("request id "+r.id+"."+r.sends+" posting");try{var c=this._conn.options.contentType||"text/xml; charset=utf-8";r.xhr.open("POST",this._conn.service,!this._conn.options.sync),void 0!==r.xhr.setRequestHeader&&r.xhr.setRequestHeader("Content-Type",c),this._conn.options.withCredentials&&(r.xhr.withCredentials=!0)}catch(e){return F.error("XHR open failed: "+e.toString()),this._conn.connected||this._conn._changeConnectStatus(F.Status.CONNFAIL,"bad-service"),void this._conn.disconnect()}var u,h=function(){if(r.date=new Date,n._conn.options.customHeaders){var e=n._conn.options.customHeaders;for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&r.xhr.setRequestHeader(t,e[t])}r.xhr.send(r.data)};1<r.sends?(u=1e3*Math.min(Math.floor(F.TIMEOUT*this.wait),Math.pow(r.sends,3)),setTimeout(function(){h()},u)):h(),r.sends++,this._conn.xmlOutput!==F.Connection.prototype.xmlOutput&&(r.xmlData.nodeName===this.strip&&r.xmlData.childNodes.length?this._conn.xmlOutput(r.xmlData.childNodes[0]):this._conn.xmlOutput(r.xmlData)),this._conn.rawOutput!==F.Connection.prototype.rawOutput&&this._conn.rawOutput(r.data)}else F.debug("_processRequest: "+(0===e?"first":"second")+" request has readyState of "+r.xhr.readyState)}}},{key:"_removeRequest",value:function(e){F.debug("removing request");for(var t=this._requests.length-1;0<=t;t--)e===this._requests[t]&&this._requests.splice(t,1);e.xhr.onreadystatechange=function(){},this._throttledRequestHandler()}},{key:"_restartRequest",value:function(e){var t=this._requests[e];null===t.dead&&(t.dead=new Date),this._processRequest(e)}},{key:"_reqToData",value:function(e){try{return e.getResponse()}catch(e){if("parsererror"!==e.message)throw e;this._conn.disconnect("strophe-parsererror")}}},{key:"_sendTerminate",value:function(e){F.debug("_sendTerminate was called");var t=this._buildBody().attrs({type:"terminate"});e&&t.cnode(e.tree());var n=new F.Request(t.tree(),this._onRequestStateChange.bind(this,this._conn._dataRecv.bind(this._conn)),t.tree().getAttribute("rid"));this._requests.push(n),this._throttledRequestHandler()}},{key:"_send",value:function(){var e=this;clearTimeout(this._conn._idleTimeout),this._throttledRequestHandler(),this._conn._idleTimeout=setTimeout(function(){return e._conn._onIdle()},100)}},{key:"_sendRestart",value:function(){this._throttledRequestHandler(),clearTimeout(this._conn._idleTimeout)}},{key:"_throttledRequestHandler",value:function(){this._requests?F.debug("_throttledRequestHandler called with "+this._requests.length+" requests"):F.debug("_throttledRequestHandler called with undefined requests"),this._requests&&0!==this._requests.length&&(0<this._requests.length&&this._processRequest(0),1<this._requests.length&&Math.abs(this._requests[0].rid-this._requests[1].rid)<this.window&&this._processRequest(1))}}],[{key:"_getRequestStatus",value:function(e,t){var n;if(4===e.xhr.readyState)try{n=e.xhr.status}catch(e){F.error("Caught an error while retrieving a request's status, reqStatus: "+n)}return void 0===n&&(n="number"==typeof t?t:0),n}}]),l}(),F.Bosh.prototype.strip=null,F.Websocket=function(){function r(e){a(this,r),this._conn=e,this.strip="wrapper";var t,n=e.service;0!==n.indexOf("ws:")&&0!==n.indexOf("wss:")&&(t="","ws"===e.options.protocol&&"https:"!==window.location.protocol?t+="ws":t+="wss",t+="://"+window.location.host,0!==n.indexOf("/")?t+=window.location.pathname+n:t+=n,e.service=t)}return i(r,[{key:"_buildStream",value:function(){return M("open",{xmlns:F.NS.FRAMING,to:this._conn.domain,version:"1.0"})}},{key:"_checkStreamError",value:function(e,t){var n=e.getElementsByTagNameNS?e.getElementsByTagNameNS(F.NS.STREAM,"error"):e.getElementsByTagName("stream:error");if(0===n.length)return!1;for(var r=n[0],s="",i="",o=0;o<r.childNodes.length;o++){var a=r.childNodes[o];if("urn:ietf:params:xml:ns:xmpp-streams"!==a.getAttribute("xmlns"))break;"text"===a.nodeName?i=a.textContent:s=a.nodeName}var c="WebSocket stream error: ";return c+=s||"unknown",i&&(c+=" - "+i),F.error(c),this._conn._changeConnectStatus(t,s),this._conn._doDisconnect(),!0}},{key:"_reset",value:function(){}},{key:"_connect",value:function(){var t=this;this._closeSocket(),this.socket=new WebSocket(this._conn.service,"xmpp"),this.socket.onopen=function(){return t._onOpen()},this.socket.onerror=function(e){return t._onError(e)},this.socket.onclose=function(e){return t._onClose(e)},this.socket.onmessage=function(e){return t._onInitialMessage(e)}}},{key:"_connect_cb",value:function(e){if(this._checkStreamError(e,F.Status.CONNFAIL))return F.Status.CONNFAIL}},{key:"_handleStreamStart",value:function(e){var t=!1,n=e.getAttribute("xmlns");"string"!=typeof n?t="Missing xmlns in <open />":n!==F.NS.FRAMING&&(t="Wrong xmlns in <open />: "+n);var r=e.getAttribute("version");return"string"!=typeof r?t="Missing version in <open />":"1.0"!==r&&(t="Wrong version in <open />: "+r),!t||(this._conn._changeConnectStatus(F.Status.CONNFAIL,t),this._conn._doDisconnect(),!1)}},{key:"_onInitialMessage",value:function(e){if(0===e.data.indexOf("<open ")||0===e.data.indexOf("<?xml")){var t=e.data.replace(/^(<\?.*?\?>\s*)*/,"");if(""===t)return;var n=(new d).parseFromString(t,"text/xml").documentElement;this._conn.xmlInput(n),this._conn.rawInput(e.data),this._handleStreamStart(n)&&this._connect_cb(n)}else{var r,s,i,o,a;0===e.data.indexOf("<close ")?(r=(new d).parseFromString(e.data,"text/xml").documentElement,this._conn.xmlInput(r),this._conn.rawInput(e.data),(s=r.getAttribute("see-other-uri"))?(0<=(i=this._conn.service).indexOf("wss:")&&0<=s.indexOf("wss:")||0<=i.indexOf("ws:"))&&(this._conn._changeConnectStatus(F.Status.REDIRECT,"Received see-other-uri, resetting connection"),this._conn.reset(),this._conn.service=s,this._connect()):(this._conn._changeConnectStatus(F.Status.CONNFAIL,"Received closing stream"),this._conn._doDisconnect())):(this._replaceMessageHandler(),o=this._streamWrap(e.data),a=(new d).parseFromString(o,"text/xml").documentElement,this._conn._connect_cb(a,null,e.data))}}},{key:"_replaceMessageHandler",value:function(){var t=this;this.socket.onmessage=function(e){return t._onMessage(e)}}},{key:"_disconnect",value:function(e){var t=this;if(this.socket&&this.socket.readyState!==WebSocket.CLOSED){e&&this._conn.send(e);var n=M("close",{xmlns:F.NS.FRAMING});this._conn.xmlOutput(n.tree());var r=F.serialize(n);this._conn.rawOutput(r);try{this.socket.send(r)}catch(e){F.warn("Couldn't send <close /> tag.")}}setTimeout(function(){return t._conn._doDisconnect},0)}},{key:"_doDisconnect",value:function(){F.debug("WebSockets _doDisconnect was called"),this._closeSocket()}},{key:"_streamWrap",value:function(e){return"<wrapper>"+e+"</wrapper>"}},{key:"_closeSocket",value:function(){if(this.socket)try{this.socket.onclose=null,this.socket.onerror=null,this.socket.onmessage=null,this.socket.close()}catch(e){F.debug(e.message)}this.socket=null}},{key:"_emptyQueue",value:function(){return!0}},{key:"_onClose",value:function(e){this._conn.connected&&!this._conn.disconnecting?(F.error("Websocket closed unexpectedly"),this._conn._doDisconnect()):e&&1006===e.code&&!this._conn.connected&&this.socket?(F.error("Websocket closed unexcectedly"),this._conn._changeConnectStatus(F.Status.CONNFAIL,"The WebSocket connection could not be established or was disconnected."),this._conn._doDisconnect()):F.debug("Websocket closed")}},{key:"_no_auth_received",value:function(e){F.error("Server did not offer a supported authentication mechanism"),this._conn._changeConnectStatus(F.Status.CONNFAIL,F.ErrorCondition.NO_AUTH_MECH),e&&e.call(this._conn),this._conn._doDisconnect()}},{key:"_onDisconnectTimeout",value:function(){}},{key:"_abortAllRequests",value:function(){}},{key:"_onError",value:function(e){F.error("Websocket error "+e),this._conn._changeConnectStatus(F.Status.CONNFAIL,"The WebSocket connection could not be established or was disconnected."),this._disconnect()}},{key:"_onIdle",value:function(){var e=this._conn._data;if(0<e.length&&!this._conn.paused){for(var t,n,r=0;r<e.length;r++){null!==e[r]&&(t=void 0,t="restart"===e[r]?this._buildStream().tree():e[r],n=F.serialize(t),this._conn.xmlOutput(t),this._conn.rawOutput(n),this.socket.send(n))}this._conn._data=[]}}},{key:"_onMessage",value:function(e){var t='<close xmlns="urn:ietf:params:xml:ns:xmpp-framing" />';if(e.data===t)return this._conn.rawInput(t),this._conn.xmlInput(e),void(this._conn.disconnecting||this._conn._doDisconnect());if(0===e.data.search("<open ")){if(r=(new d).parseFromString(e.data,"text/xml").documentElement,!this._handleStreamStart(r))return}else var n=this._streamWrap(e.data),r=(new d).parseFromString(n,"text/xml").documentElement;return this._checkStreamError(r,F.Status.ERROR)?void 0:this._conn.disconnecting&&"presence"===r.firstChild.nodeName&&"unavailable"===r.firstChild.getAttribute("type")?(this._conn.xmlInput(r),void this._conn.rawInput(F.serialize(r))):void this._conn._dataRecv(r,e.data)}},{key:"_onOpen",value:function(){F.debug("Websocket open");var e=this._buildStream();this._conn.xmlOutput(e.tree());var t=F.serialize(e);this._conn.rawOutput(t),this.socket.send(t)}},{key:"_reqToData",value:function(e){return e}},{key:"_send",value:function(){this._conn.flush()}},{key:"_sendRestart",value:function(){clearTimeout(this._conn._idleTimeout),this._conn._onIdle.bind(this._conn)()}}]),r}();var B={};B.debug=F.LogLevel.DEBUG,B.info=F.LogLevel.INFO,B.warn=F.LogLevel.WARN,B.error=F.LogLevel.ERROR,B.fatal=F.LogLevel.FATAL,F.WorkerWebsocket=function(){o(r,F.Websocket);var n=u(r);function r(e){var t;return a(this,r),(t=n.call(this,e))._conn=e,t.worker=new SharedWorker(t._conn.options.worker,"Strophe XMPP Connection"),t.worker.onerror=function(e){var t;null!==(t=console)&&void 0!==t&&t.error(e),F.log(F.LogLevel.ERROR,"Shared Worker Error: ".concat(e))},t}return i(r,[{key:"_connect",value:function(){var t=this;this._messageHandler=function(e){return t._onInitialMessage(e)},this.worker.port.start(),this.worker.port.onmessage=function(e){return t._onWorkerMessage(e)},this.worker.port.postMessage(["_connect",this._conn.service,this._conn.jid])}},{key:"_attach",value:function(e){var t=this;this._messageHandler=function(e){return t._onMessage(e)},this._conn.connect_callback=e,this.worker.port.start(),this.worker.port.onmessage=function(e){return t._onWorkerMessage(e)},this.worker.port.postMessage(["_attach",this._conn.service])}},{key:"_attachCallback",value:function(e,t){e===F.Status.ATTACHED?(this._conn.jid=t,this._conn.authenticated=!0,this._conn.connected=!0,this._conn.restored=!0,this._conn._changeConnectStatus(F.Status.ATTACHED)):e===F.Status.ATTACHFAIL&&(this._conn.authenticated=!1,this._conn.connected=!1,this._conn.restored=!1,this._conn._changeConnectStatus(F.Status.ATTACHFAIL))}},{key:"_disconnect",value:function(e,t){t&&this._conn.send(t);var n=M("close",{xmlns:F.NS.FRAMING});this._conn.xmlOutput(n.tree());var r=F.serialize(n);this._conn.rawOutput(r),this.worker.port.postMessage(["send",r]),this._conn._doDisconnect()}},{key:"_onClose",value:function(e){this._conn.connected&&!this._conn.disconnecting?(F.error("Websocket closed unexpectedly"),this._conn._doDisconnect()):e&&1006===e.code&&!this._conn.connected?(F.error("Websocket closed unexcectedly"),this._conn._changeConnectStatus(F.Status.CONNFAIL,"The WebSocket connection could not be established or was disconnected."),this._conn._doDisconnect()):F.debug("Websocket closed")}},{key:"_closeSocket",value:function(){this.worker.port.postMessage(["_closeSocket"])}},{key:"_replaceMessageHandler",value:function(){var t=this;this._messageHandler=function(e){return t._onMessage(e)}}},{key:"_onWorkerMessage",value:function(e){var t,n,r=e.data,s=r[0];if("_onMessage"===s)this._messageHandler(r[1]);else if(s in this)try{this[s].apply(this,e.data.slice(1))}catch(e){F.log(F.LogLevel.ERROR,e)}else{"log"===s?(t=r[1],n=r[2],F.log(B[t],n)):F.log(F.LogLevel.ERROR,"Found unhandled service worker message: ".concat(r))}}},{key:"socket",get:function(){var t=this;return{send:function(e){return t.worker.port.postMessage(["send",e])}}}}]),r}(),t.$build=j.$build,t.$iq=j.$iq,t.$msg=j.$msg,t.$pres=j.$pres,t.Strophe=j.Strophe,e.$build=M,e.$iq=q,e.$msg=L,e.$pres=D,e.Strophe=F,Object.defineProperty(e,"__esModule",{value:!0})});