PORTNAME=	APCu
PORTVERSION=	5.1.18
PORTREVISION=	0
CATEGORIES=	devel
MASTER_SITES=	http://pecl.php.net/get/
PKGNAMEPREFIX=	php73-
DISTNAME=	${PORTNAME:tl}-${PORTVERSION}
DIST_SUBDIR=	PECL

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	APC User Caching

LICENSE=	PHP301

CONFLICTS_INSTALL=	pecl-APC

IGNORE_WITH_PHP=	70 71 72
PHP_VER=	73
USES=		localbase php:ext tar:tgz
PHP_MODNAME=	${PORTNAME:tl}

.include <bsd.port.mk>
