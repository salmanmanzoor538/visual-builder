# Domains

The initial route uses `/sites/{website}/{page?}`. Custom domains will store normalized hostnames per website, require DNS ownership verification, reject reserved/internal hosts, and resolve the tenant before page lookup. TLS provisioning and renewal belong to deployment infrastructure.
