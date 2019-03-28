# Resilience Testing with Docker for Mac & `tc`

## Synopsis

This repository contains a macOS Keynote presentation about how to perform 
resilience tests under Docker for Mac with `tc`. It focuses on **delaying**
the (isolated) service and client network in order to test and detect 
**connection** and **R/W** timeout problems.

## Guide

0.  Run the following command from the project root. This will install all 
    dependencies.

        script/bootstrap

1.  Checkout the commit tagged `start`

2.  Demonstration:

        docker-compose up

3.  Run the presentation from `apps/resilience-talk/res/`

4.  Stop demo

        Ctrl + C
        docker-compose down -v

5.  Accompany presentation with commits starting with "ACT-"

## How to...

### ...add / delete a delay on the `slownet` network?

While running the demo, `screen` into `Docker for Mac` VM and investigate the 
networks:

**Note**: `>` denote output examples.

    # show id of network "slownet"
    (host) docker network ls | grep slownet
    (host) > f5c43d88f2b6        playground_slownet      bridge              local

    # screen tunnel into the xhyve VM
    (vm) screen ~/Library/Containers/com.docker.docker/Data/vms/0/tty

    # find network
    (vm) ifconfig | grep <id from first command>
    (vm) > br-f5c43d88f2b6 Link encap:Ethernet  HWaddr 02:42:E7:82:DF:BA

    # add a delay rule to the network interface
    (vm) tc qdisc add dev br-c0aca63f9c15 root netem delay 500ms
        
    # remove delay rule from the network interface
    (vm) tc qdisc del dev br-f5c43d88f2b6 root netem

### ...manage `screen` sessions?

    # disconnect from session but leave it open in the background
    Ctrl-a d
    # list sessions still running in the background
    screen -ls
    # reconnect to a session
    screen -r <session id>
    # kill the current active session
    Ctrl-a k

## Links

*   [Slow down your internet with tc](https://jvns.ca/blog/2017/04/01/slow-down-your-internet-with-tc/)
*   [How can I rate limit network traffic on a docker container](https://stackoverflow.com/questions/25497523/how-can-i-rate-limit-network-traffic-on-a-docker-container)
*   [Network emulation for Docker containers](https://alexei-led.github.io/post/pumba_docker_netem/)
*   [pgorczak/netz](https://github.com/pgorczak/netz)
*   [lukaszlach/docker-tc](https://github.com/lukaszlach/docker-tc)
*   [thombashi/tcconfig](https://github.com/thombashi/tcconfig)
*   [alexei-led/pumba](https://github.com/alexei-led/pumba)
*   [Apply NetEM WAN delay on a docker container interface // Docker Network for throttling a single service only](https://stackoverflow.com/questions/41899906/apply-netem-wan-delay-on-a-docker-container-interface)
*   [Docker for Mac Shell](https://gist.github.com/BretFisher/5e1a0c7bcca4c735e716abf62afad389)
*   [`tc` command examples](https://jvns.ca/blog/2017/04/01/slow-down-your-internet-with-tc/)
*   [more `tc` command examples](https://serverfault.com/questions/207162/simulating-a-slow-connection-with-tc)
