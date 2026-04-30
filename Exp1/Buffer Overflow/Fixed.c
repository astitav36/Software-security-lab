#include <stdio.h>
#include <string.h>

int main() {
    char buffer[10];

    printf("Enter your name: ");
    scanf("%10s", &buffer); 

    printf("Hello %s\n", buffer);
    return 0;
}