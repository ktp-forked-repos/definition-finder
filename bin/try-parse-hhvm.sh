#!/bin/sh
#  Copyright (c) 2015, Facebook, Inc.
#  All rights reserved.
#
#  This source code is licensed under the MIT style license found in the
#  LICENSE file in the root directory of this source tree.
#

set -e

if [ ! -d "$1" ]; then
  echo "Usage: $0 /path/to/hhvm/source/tree"
  exit 1
fi

HHVM="$1"
TRY_PARSE="$(dirname "$0")/try-parse.php"
if $(which gfind) 2>&1 >/dev/null; then
  FIND=gfind # MacOS
else
  FIND=find
fi

# blacklist egrep is for usage of dict, vec, keyset, and facebook/hhvm#7668
$FIND \
  "$HHVM/hphp/test/zend" \
  "$HHVM/hphp/test/quick" \
  "$HHVM/hphp/test/slow" \
  "$HHVM/hphp/runtime/ext" \
  "$HHVM/hphp/system/php" \
  "$HHVM/hphp/hack/hhi" \
  -name '*.php' -o -name '*.hhi' \
| egrep -v 'quick/(dict|keyset|vec)/static.php|quick/init-basic.php|slow/parser/unicode-literal-error.php' \
| xargs "$TRY_PARSE"
