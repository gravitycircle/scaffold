#!/bin/sh

# eppz!tools

# SourceTree Custom Action parameter hooks goes like
# $PATH <ftp username> <ftp password> <ftp path>

REPOSITORY_PATH=$(dirname "$0")
# FTP_USERNAME="$2"
# FTP_PASSWORD="$3"
# FTP_PATH="$4"
FTP_CONFIG=false
SCOPE=$1
FNAME=$2
URL="http://richardbryanong.com/sites/tools/json-parser/index.php"


if [ ! -f "$REPOSITORY_PATH/$FNAME" ]; then
    echo "$REPOSITORY_PATH/$FNAME cannot be found. Did you type it in correctly?"
else
	FTP_CONFIG=`cat "$REPOSITORY_PATH/$FNAME"`
	# echo $FTP_CONFIG

	TEST=`curl --data-urlencode "parse=$FTP_CONFIG" "$URL"`

	IFS=', ' read -r -a pairs <<< $TEST
	FLOATING='';
	USR="";
	PWD="";
	FTP="";
	PATH="";

	declare -a conf
	for index in "${!pairs[@]}"
	do
	   FLOATING="${pairs[index]/=/, }"
	   IFS=', ' read -r -a vars <<< "$FLOATING"
	   IND="${vars[0]}"
	   VLE="${vars[1]}"
	   
	   if [ "$IND" == "url" ]; then
	   	FTP=$VLE
	   fi

	   if [ "$IND" == "usr" ]; then
	   	USR=$VLE
	   fi

	   if [ "$IND" == "pwd" ]; then
	   	PWD=$VLE
	   fi

	   if [ "$IND" == "pth" ]; then
	   	PATH=$VLE
	   fi
	done


	# echo "Switching directory to repository root at '$REPOSITORY_PATH'"
	# cd "$REPOSITORY_PATH"

	if [ "$USR" == "" ] || [ "$PWD" == "" ] || [ "$FTP" == "" ] || [ "$PATH" == "" ]; then
		echo "Issue: Config file specified not complete.";
	else
		# echo "Pushing latest checkout to '$FTP_PATH' with the given credentials"
		if [ "$SCOPE" == "default" ]; then
			/usr/local/bin/git config git-ftp.user $USR
			/usr/local/bin/git config git-ftp.url "$FTP$PATH"
			/usr/local/bin/git config git-ftp.password $PWD
		else
			`/usr/local/bin/git config git-ftp.$SCOPE.user $USR`
			`/usr/local/bin/git config git-ftp.$SCOPE.url $FTP$PATH`
			`/usr/local/bin/git config git-ftp.$SCOPE.password $PWD`
		fi
	fi
fi



# /usr/local/bin/git-ftp push -u $FTP_USERNAME -p $FTP_PASSWORD "$FTP_PATH"
# /usr/local/bin/git-ftp init -u $FTP_USERNAME -p $FTP_PASSWORD "$FTP_PATH"

exit 0