#!/bin/bash

find ./ -name "*.sw" -exec cat "{}" \;|sort|uniq>all.swf
