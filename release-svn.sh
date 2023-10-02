# This script folder is used to automatically release new version of plugin code to wordpress
# SVN needs to be installed on the local computer

rm -rf temp-svn-dir

# Ask for version
read -p "Which version are you releasing? (ex: 1.0) " plugin_version
if [[ $plugin_version == "" ]]; then exit; fi

read -p "Have you edited the readme.txt and sertifier.php files? (Y/n) " readme_edited
if [[ $readme_edited != "Y" ]]; then exit; fi

# Edit readme.txt for new plugin version
#echo "Changing readme.txt with new stable plugin version"
#echo "Stable tag: ${plugin_version}" | sed -r "s/Stable tag: (?!\n).*\n/\1/g"
# sed -i '' -E "s/Stable tag: .*\n/Stable tag: ${plugin_version}\n/g" readme.txt

# Open temp svn directory and init svn project
mkdir -p temp-svn-dir
svn co https://plugins.svn.wordpress.org/sertifier-certificates-open-badges temp-svn-dir

# Copy all files to trunk
cp -R assets temp-svn-dir/trunk/
cp -R classes temp-svn-dir/trunk/
cp -R pages temp-svn-dir/trunk/
cp -R templates temp-svn-dir/trunk/
cp -R readme.txt temp-svn-dir/trunk/
cp -R sertifier.php temp-svn-dir/trunk/

# Copy svn-assets
mkdir -p temp-svn-dir/assets
cp -R svn-assets/ temp-svn-dir/assets/

# Move new trunk codes to new version and add them 
cd temp-svn-dir
svn cp trunk tags/$plugin_version
svn add --force trunk/*
svn add --force tags/$plugin_version/*
svn add --force assets/*
svn ci -m "New version release: ${plugin_version}" --username sertifier
cd ..
rm -rf temp-svn-dir

git add .
git commit -m "New release version: ${plugin_version}"
git push origin

