#!/bin/bash
#循环29次
#间隔的秒数，不能大于60
step=1

for (( i = 0; i < 60; i=(i+step) ));
do
    $(php '/opt/script2.php')
    sleep $step
done

exit 0