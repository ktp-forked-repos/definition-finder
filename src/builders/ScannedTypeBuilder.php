<?hh // strict
/*
 *  Copyright (c) 2015-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\DefinitionFinder;

class ScannedTypeBuilder extends ScannedSingleTypeBuilder<ScannedType> {
  <<__Override>>
  public function build(): ScannedType {
    return new ScannedType(
      $this->ast,
      $this->name,
      $this->getDefinitionContext(),
      /* attributes = */ dict[],
      $this->docblock,
    );
  }
}
