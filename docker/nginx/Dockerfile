ARG NGINX_VERSION=1.27.5
ARG HEADERS_MORE_VERSION=0.38

FROM nginx:${NGINX_VERSION}-alpine AS module-builder

ARG NGINX_VERSION
ARG HEADERS_MORE_VERSION
ARG NGINX_SHA256=e96acebb9c2a6db8a000c3dd1b32ecba1b810f0cd586232d4d921e376674dd0e
ARG HEADERS_MORE_SHA256=febf7271c0c3de69adbd02c1e98ee43e91a60eeb6b27abfb77b5b206fda5215a

RUN apk add --no-cache build-base linux-headers openssl-dev pcre2-dev zlib-dev \
    && mkdir -p /tmp/build \
    && cd /tmp/build \
    && wget -q "https://nginx.org/download/nginx-${NGINX_VERSION}.tar.gz" \
    && echo "${NGINX_SHA256}  nginx-${NGINX_VERSION}.tar.gz" | sha256sum -c - \
    && wget -q -O "headers-more-${HEADERS_MORE_VERSION}.tar.gz" \
        "https://github.com/openresty/headers-more-nginx-module/archive/refs/tags/v${HEADERS_MORE_VERSION}.tar.gz" \
    && echo "${HEADERS_MORE_SHA256}  headers-more-${HEADERS_MORE_VERSION}.tar.gz" | sha256sum -c - \
    && tar -xzf "nginx-${NGINX_VERSION}.tar.gz" \
    && tar -xzf "headers-more-${HEADERS_MORE_VERSION}.tar.gz" \
    && cd "nginx-${NGINX_VERSION}" \
    && ./configure \
        --with-compat \
        --add-dynamic-module="/tmp/build/headers-more-nginx-module-${HEADERS_MORE_VERSION}" \
    && make modules

FROM nginx:${NGINX_VERSION}-alpine

ARG NGINX_VERSION

COPY --from=module-builder \
    /tmp/build/nginx-${NGINX_VERSION}/objs/ngx_http_headers_more_filter_module.so \
    /usr/lib/nginx/modules/ngx_http_headers_more_filter_module.so

RUN sed -i '1i load_module modules/ngx_http_headers_more_filter_module.so;' /etc/nginx/nginx.conf
