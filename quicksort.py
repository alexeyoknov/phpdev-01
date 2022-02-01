from datetime import datetime
from random import randrange

# Создает массив случайных целых чисел размера n
def rand_arr(n):
    arr = []
    for i in range(n):
        arr.append(randrange(n))
    return arr

def swap(arr,i,m):
    arr[i], arr[m] = arr[m], arr[i]

def partition(arr,left,right):
    pivot = arr[int((left+right)/2)]
    i = left
    j = right
    while 1:
        while (arr[i] < pivot) and (i<=right):
            i+=1
        while (arr[j] > pivot) and (j>=left):
            j-=1
        if i >= j:
           return j
        swap(arr,i,j)
        i+=1
        j-=1
            

def quick_sort(arr,left,right):
    if left<right:
        m = partition(arr,left, right)
        arr=quick_sort(arr,left, m)
        arr=quick_sort(arr,m+1, right)

    return arr

n=100
a=rand_arr(n)
if (len(a) <= 100):
    print("\nOriginal array:\n")
    print(a)

t = datetime.now()
a = quick_sort(a,0,len(a)-1)
t = datetime.now()- t

print('\nQuick sort time: ',t,'\n')
if (len(a) <= 100):
    print("Sorted array: \n")
    print(a)
    print('\n')