Name:           rpxy-webui
Version:        0.1
Release:        1%{?dist}
Summary:        WebApp to manage rpxy instance configuration

License:        GPL-3.0
URL:            https://github.com/Gamerboy59/rpxy-webui
Source0:        rpxy-webui-0.1.tar.gz
BuildArch:      noarch

Requires:       httpd, php-fpm >= 8.2, php-mbstring >= 8.2, php-curl >= 8.2, php-pdo >= 8.2, php-xml >= 8.2, php-pecl-zip >= 1.19
Requires(post): python3-policycoreutils
Requires(postun): python3-policycoreutils

%description
This rpm installs rpxy-webui from github sources into /usr/share/rpxy-webui/ and configures apache to display it when accessing /rpxy-webui/.

%prep
%autosetup

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_localstatedir}/www/
cp -r data/ $RPM_BUILD_ROOT%{_localstatedir}/www/rpxy-webui/
mkdir -p $RPM_BUILD_ROOT%{_sysconfdir}/httpd/conf.d/
cp conf/apache.conf $RPM_BUILD_ROOT%{_sysconfdir}/httpd/conf.d/rpxy-webui.conf

%clean
rm -rf $RPM_BUILD_ROOT

%files
%license %{_localstatedir}/www/rpxy-webui/LICENSE
%doc %{_localstatedir}/www/rpxy-webui/README.md
%attr(-, apache, apache) %{_localstatedir}/www/rpxy-webui/
%{_sysconfdir}/httpd/conf.d/rpxy-webui.conf

%pre
if [ $1 -eq 1 ] ; then
    DISTRIBUTION="$(cat /etc/os-release | awk -F '=' '/^ID=/{print $2}' | tr -d '"')"
    VERSION="$(cat /etc/os-release | awk -F '=' '/^VERSION_ID=/{print $2}' | awk -F '.' '{print $1}' | tr -d '"')"
    case $DISTRIBUTION in
      rocky|alma)
        if [ $VERSION = "9" ]; then
            dnf module enable php:8.2
        fi
        ;;
      *)
        echo -e "\033[0;32mError setting php version\033[0m"
        exit 1
        ;;
    esac
fi

%post
if [ $1 -eq 1 ] ; then
    touch %{_localstatedir}/www/rpxy-webui/database/database.sqlite
    chown apache:apache %{_localstatedir}/www/rpxy-webui/database/database.sqlite
    semanage fcontext -a -t httpd_sys_rw_content_t '%{_localstatedir}/www/rpxy-webui/database/database.sqlite'
    semanage fcontext -a -t httpd_sys_rw_content_t '%{_localstatedir}/www/rpxy-webui/storage(/.*)?'
    semanage fcontext -a -t httpd_sys_rw_content_t '%{_localstatedir}/www/rpxy-webui/bootstrap/cache'
    restorecon -R %{_localstatedir}/www/rpxy-webui/
    php %{_localstatedir}/www/rpxy-webui/artisan key:generate
    php %{_localstatedir}/www/rpxy-webui/artisan migrate --seed
    service php-fpm restart
    service httpd restart
elif [ $1 -gt 1 ] ; then
    php %{_localstatedir}/www/rpxy-webui/artisan migrate
fi

%postun
semanage fcontext -d -t httpd_sys_rw_content_t '%{_localstatedir}/www/rpxy-webui/database/database.sqlite'
semanage fcontext -d -t httpd_sys_rw_content_t '%{_localstatedir}/www/rpxy-webui/storage(/.*)?'
semanage fcontext -d -t httpd_sys_rw_content_t '%{_localstatedir}/www/rpxy-webui/bootstrap/cache'

%changelog
* Thu Aug 29 2024 Gamerboy59 - 0.2
- Adding ACME support and manual certificate import.
- It is compatible with rpxy 0.9.x or newer.
* Sun Jul 21 2024 Gamerboy59 - 0.1
- This is the first release.
- It is compatible with rpxy 0.8.x or newer.
