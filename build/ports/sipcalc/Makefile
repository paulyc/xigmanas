PORTNAME=		sipcalc
PORTVERSION=	1.1.6
CATEGORIES=		net-mgmt
COMMENT=		IP subnet calculator with IPv6 support
MAINTAINER=		zoon01@xigmanas.com

MASTER_SITES=	http://www.routemeister.net/projects/sipcalc/files/

USE_GETOPT_LONG=	yes
GNU_CONFIGURE=	yes
NO_PKG_REGISTER=	yes

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/sipcalc ${XIGMANAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>