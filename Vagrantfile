Vagrant.configure("2") do |config|

  config.vm.box = "ncaro/php7-debian8-apache-nginx-mysql"


  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  config.vm.network "public_network"

  config.vm.synced_folder "code", "/var/www/html/code"

  config.vm.provision "shell", inline: "sudo apt-get update"
  config.vm.provision "shell", inline: "sudo service php5-fpm stop && sudo service php7-fpm stop && sudo service php7-fpm start",run:'always'
end
