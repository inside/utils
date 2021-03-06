#!/usr/bin/env python

import sys
import re
import urllib2

def get_file(file):
    if re.search('^http://', file):
        try:
            f = urllib2.urlopen(file)
        except urllib2.URLError, e:
            print e
            sys.exit(1)
    else:
        try:
            f = open(file, 'r')
        except IOError, e:
            print e
            sys.exit(1)

    str = f.read()
    f.close()
    return str

def get_base_url():
    return 'http://www.junodownload.com/'

# Main
if len(sys.argv) <= 1:
    print 'Usage: ' + sys.argv[0] + ' [juno mailing list link or html file]'
    sys.exit(1)

match = re.search('href="([^"]+\.m3u)"', get_file(sys.argv[1]))

if match == None:
    print 'Couldn\'t find m3u link'
    sys.exit(1)

m3uUrl = get_base_url() + match.group(1)
content = get_file(m3uUrl)

print content
