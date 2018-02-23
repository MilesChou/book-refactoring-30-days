from os import listdir
from sys import argv

print("argv[1] " + argv[1])

relativePath = argv[1] 

for files in listdir(argv[1]): # get the file name only
    if files == "day30.md": # skip the last one
        continue

    sourcePath = relativePath + files # get the path of files

    print('get file:' + sourcePath)
    
    nextfileName = "day" + str(int(files[3:5]) + 1).zfill(2) + ".md"
    hyperlink = "Go to next:\n[" + nextfileName[:-3] + "](./" + nextfileName +")"
    
    addText = """
* * *
""" 
    addText += hyperlink
    print("addText:" + addText)
    with open(sourcePath, 'a') as sourceFile: # append content	
        sourceFile.write(addText)
