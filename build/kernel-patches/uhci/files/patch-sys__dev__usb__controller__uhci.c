--- sys/dev/usb/controller/uhci.c.orig	2016-03-13 20:04:25.527262000 +0100
+++ sys/dev/usb/controller/uhci.c	2016-03-13 22:24:56.000000000 +0100
@@ -1478,7 +1478,8 @@
 	    UHCI_STS_USBEI |
 	    UHCI_STS_RD |
 	    UHCI_STS_HSE |
-	    UHCI_STS_HCPE);
+	    UHCI_STS_HCPE |
+	    UHCI_STS_HCH);
 
 	if (status == 0) {
 		/* nothing to acknowledge */
