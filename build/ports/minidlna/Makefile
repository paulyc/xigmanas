PORTNAME=	minidlna
PORTVERSION=	1.2.1
PORTREVISION=   6
PORTEPOCH=      1
CATEGORIES=	net multimedia www

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Media-server compatible with "Digital Life Network Alliance"

LIB_DEPENDS=	libsqlite3.so:databases/sqlite3 \
		libexif.so:graphics/libexif \
		libid3tag.so:audio/libid3tag \
		libogg.so:audio/libogg \
		libvorbis.so:audio/libvorbis \
		libFLAC.so:audio/flac \
		libavformat.so:multimedia/ffmpeg

GNU_CONFIGURE=	yes
CONFIGURE_ARGS=	--with-db-path=/var/db/minidlna \
		--with-os-url="https://www.xigmanas.com"
CPPFLAGS+=	-I${LOCALBASE}/include
CFLAGS+=	-DXigmaNAS
LIBS+=		-L${LOCALBASE}/lib
USES=		autoreconf gettext-tools iconv jpeg

USE_GITHUB=     yes 
GH_ACCOUNT=     glebius 
GH_TAGNAME=     f06964a 


PKGMESSAGE=	${WRKSRC}/pkg-message
SUB_FILES+=	pkg-message
USE_RC_SUBR=	minidlna
SUB_LIST+=	USER=${USERS}

USERS=	dlna
GROUPS=	dlna

OPTIONS_DEFINE=	DEBUG NLS
OPTIONS_SUB=	yes

DEBUG_CFLAGS=	-DDEBUG
NLS_USES=	gettext-runtime

.include <bsd.port.options.mk>

# XigmaNAS
NO_PKG_REGISTER=	yes
ICONSDIR=${MASTERDIR}/icons
XIGMANAS_ICONS=${WRKSRC}/xigmanas-icons.c

post-configure:
	@echo "creating icon file..."
	@(cd ${ICONSDIR}; sh mkicons.sh > ${XIGMANAS_ICONS})

post-install:
	${INSTALL_MAN} ${WRKSRC}/*.5 ${STAGEDIR}${MANPREFIX}/man/man5/
	${INSTALL_MAN} ${WRKSRC}/*.8 ${STAGEDIR}${MANPREFIX}/man/man8/
	${INSTALL_DATA} ${WRKSRC}/minidlna.conf ${STAGEDIR}${PREFIX}/etc/minidlna.conf.sample

# XigmaNAS install
	@echo "install for ${XIGMANAS_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/sbin/minidlnad ${XIGMANAS_ROOTFS}/usr/local/sbin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/minidlna.in ${XIGMANAS_ROOTFS}/etc/rc.d/minidlna

.include <bsd.port.mk>
