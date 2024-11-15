#!/bin/bash

# 获取脚本所在目录作为StartDir
StartDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
workDir="$(dirname "$StartDir")"
workParent="$(dirname "$workDir")"
zipexe="/usr/bin/7z"  # 请确保此路径为7z可执行文件的路径

indexFile="$workDir/Plugin.php"
excludeFile="$workDir/tools/pack.exclude"
archiveNameOriginal=""
package=""
version=""

# 检查 index.php 或 Plugin.php
if [[ ! -f "$indexFile" ]]; then
  indexFile="$workDir/index.php"
  if [[ ! -f "$indexFile" ]]; then
    echo "Do Nothing"
    exit
  fi
fi
# 提取包名和版本号
package=$(grep -m 1 "@package" "$indexFile" | awk '{print $3}')
version=$(grep -m 1 "@version" "$indexFile" | awk '{print $3}')
stamp=$(date +%Y%m%d)
archiveName="${package}-${version}-${stamp}.zip"
excludeList=($(cat "$excludeFile"))
tempExcludeFile="$workDir/tools/pack.exclude.tmp"

# 如果存在临时文件，删除它们
[[ -f "$tempExcludeFile" ]] && rm -f "$tempExcludeFile"
[[ -f "$workDir/pack/$archiveNameOriginal" ]] && rm -f "$workDir/pack/$archiveNameOriginal"

# 创建排除文件
for exclude in "${excludeList[@]}"; do
  echo "$package/$exclude" >> "$tempExcludeFile"
done

# 压缩原始归档文件
echo "Compressing files to $workDir/pack/$archiveName"
cd "$workParent" || exit
"$zipexe" a -tzip -r -x@"$tempExcludeFile" -spf "$workDir/pack/$archiveName" "$package"

# 清理
rm -f "$tempExcludeFile"
cd "$StartDir" || exit