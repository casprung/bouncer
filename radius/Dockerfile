FROM alpine:3.7

RUN apk update && apk upgrade && \
    apk add --update freeradius freeradius-mysql freeradius-radclient gettext bash && \
    rm /var/cache/apk/*

COPY --chown=root:radius templates/default /etc/raddb/sites-available/default
COPY --chown=root:radius templates/portal /etc/raddb/policy.d/portal
COPY --chown=root:radius templates/authorize /etc/raddb/mods-config/files/authorize
COPY templates/healthcheck.pkt healthcheck.pkt
COPY templates/sql.template sql.template
COPY templates/clients.conf.template clients.conf.template

COPY docker-entrypoint.sh /usr/local/bin/
RUN ln -s usr/local/bin/docker-entrypoint.sh /entrypoint.sh # backwards compat
RUN ["chmod", "+x", "usr/local/bin/docker-entrypoint.sh"]

EXPOSE 1812/udp
EXPOSE 1813/udp

HEALTHCHECK --interval=60s --timeout=4s CMD /usr/bin/radclient -q -r 1 -t 3 -f /healthcheck.pkt 127.0.0.1 auth qmf7B4LJQxKmqkAQA

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["/usr/sbin/radiusd", "-f"]
